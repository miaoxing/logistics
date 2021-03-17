<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Cart\Service\Cart;

class ShippingTpl extends \Miaoxing\Plugin\BaseService
{
    const CUSTOM_RULE = 0;

    /**
     * {@inheritdoc}
     */
    protected $table = 'shippingTpls';

    /**
     * {@inheritdoc}
     */
    protected $data = [
        'freeShipping' => 0,
        'logisticsIds' => [],
        'useLogisticsIds' => [
            1 => 0, // 默认快递启用自定义规则
        ],
    ];

    /**
     * 默认的配送服务
     *
     * @var array
     */
    protected $defaultService = [
        'id' => 1,
        'name' => '快递',
        'fee' => '0.00',
    ];

    /**
     * @var ShippingTplRule[]|ShippingTplRule
     */
    protected $rules;

    /**
     * 特殊城市与所属"市辖区"ID的对应关系
     *
     * @var array
     */
    protected $cityIds = [
        '北京市' => 110100,
        '天津市' => 120100,
        '上海市' => 310100,
        '重庆市' => 500100,
    ];

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'db' => 'app.db',
    ];

    /**
     * Record: 获取运费模板规则
     *
     * @return ShippingTplRule|ShippingTplRule[]
     */
    public function getRules()
    {
        $this->rules || $this->rules = wei()->shippingTplRule()->findAll(['shippingTplId' => $this['id']]);

        return $this->rules;
    }

    /**
     * Record: 设置运费模板规则
     *
     * @param ShippingTplRule $shippingTplRules
     * @return $this
     */
    public function setRules(ShippingTplRule $shippingTplRules)
    {
        $this->rules = $shippingTplRules;

        return $this;
    }

    /**
     * 转换为数组时,附加规则的值
     *
     * @param array $returnFields
     * @return array
     */
    public function toArray($returnFields = [])
    {
        $data = parent::toArray($returnFields);
        if (!$this->isColl) {
            $data['rules'] = $this->getRules()->toArray();
        }

        return $data;
    }

    /**
     * Repo: 根据城市,获取各个物流服务商匹配的规则
     *
     * @param int $cityId 城市编号,如上海市辖区310000,深圳440300
     * @return ShippingTplRule|ShippingTplRule[]
     */
    public function getRulesByCity($cityId = null)
    {
        // 如果不是数字ID,查表转换为数字ID
        if (!is_numeric($cityId)) {
            // 如果是特殊城市,如上海市,转换为上海市"市辖区"的ID
            if (isset($this->cityIds[$cityId])) {
                $cityId = $this->cityIds[$cityId];
            } else {
                $area = wei()->appDb('areas')->select('id')->find(['name' => $cityId]);
                if ($area) {
                    $cityId = $area['id'];
                }
            }
        }

        $rules = $this->getRules();
        $logisticsRules = [];
        foreach ($rules as $rule) {
            $logisticsRules[$rule['logisticsId']][] = $rule;
        }

        // 处理使用"xx"运费规则
        foreach ($this['logisticsIds'] as $logisticsId) {
            $useId = $this['useLogisticsIds'][$logisticsId];
            if (isset($logisticsRules[$useId], $logisticsRules[$logisticsId])) {
                // 清空原来的规则
                $logisticsRules[$logisticsId] = [];
                foreach ($logisticsRules[$useId] as $rule) {
                    $useRule = clone $rule;
                    $useRule['logisticsId'] = $logisticsId;
                    $logisticsRules[$logisticsId][] = $useRule;
                }
            }
        }

        $matches = [];
        $default = null;
        foreach ($logisticsRules as $logisticsId => $rules) {
            foreach ($rules as $rule) {
                if ($rule['isDefault']) {
                    $default = $rule;
                }
                if (in_array($cityId, $rule['areas'], true)) {
                    $matches[$logisticsId] = $rule;
                    break;
                }
            }
            // 没有匹配的地区,使用默认
            if (!isset($matches[$logisticsId])) {
                $matches[$logisticsId] = $default;
            }
        }

        return wei()->shippingTplRule()->beColl()->setData($matches);
    }

    /**
     * Record: 根据城市,获取配送服务
     *
     * @param string $city
     * @return array 返回数组包含city和services
     */
    public function getShippingConfig($city = null)
    {
        $services = [];
        if ($this->isNew || $this['freeShipping']) {
            $services[] = $this->defaultService;
        } elseif (1 === $this->getRules()->length()) {
            // 只有默认规则，直接显示，不用读取城市计算
            $rule = $this->getRules()[0];
            $services[] = [
                'id' => $rule['logisticsId'],
                'name' => wei()->logistics->getName($rule['logisticsId']),
                'fee' => $rule['startFee'],
            ];
        } else {
            if (!$city) {
                $ipInfo = wei()->lbs->getIpInfo();
                $city = $ipInfo['city'];
            }
            $logistics = wei()->logistics;
            $rules = $this->getRulesByCity($city);
            foreach ($rules as $rule) {
                $services[] = [
                    'id' => $rule['logisticsId'],
                    'name' => $logistics->getName($rule['logisticsId']),
                    'fee' => $rule['startFee'],
                ];
            }
        }

        return [
            'city' => $city,
            'services' => $services,
        ];
    }

    /**
     * Repo: 根据购物车和地址,获取可用的配送服务
     *
     * <code>
     * $return = [
     *   [
     *      'id' => '1', // 物流编号
     *      'name' => '' // 物流服务名称
     *      'fee' => '10.00', // 运费
     *   ]
     * ];
     * </code>
     *
     * @param Cart|\Miaoxing\Cart\Service\Cart[] $carts
     * @param \Miaoxing\Address\Service\Address $address
     * @return array
     */
    public function getShippingServices(Cart $carts, \Miaoxing\Address\Service\Address $address = null)
    {
        $cityId = $address ? $address->getCityIdOrName() : null;

        // 1. 构造物流配置数据
        $cartNum = $carts->length();
        $logisticsConfigs = [];
        foreach ($carts as $cart) {
            $product = $cart->getProduct();

            // 对虚拟商品不加入计算
            if ($product['isVirtual']) {
                --$cartNum;
                continue;
            }

            $shippingTpl = $product->getShippingTpl();

            // 对包邮或未设置运费模板的商品不加入计算
            if ($shippingTpl['freeShipping'] || $shippingTpl->isNew) {
                --$cartNum;
                continue;
            }

            $rules = $product->getShippingTpl()->getRulesByCity($cityId);

            // 对没有设置运费规则的商品不加入计算
            if (0 == $rules->length()) {
                --$cartNum;
                continue;
            }

            foreach ($rules as $rule) {
                $logisticsConfigs[$rule['logisticsId']][] = [
                    'cart' => $cart,
                    'rule' => $rule,
                ];
            }
        }

        // 2. 对所有商品都支持的物流方式,计算物流费用
        $services = [];
        $logistics = wei()->logistics;
        foreach ($logisticsConfigs as $logisticsId => $configs) {
            if (count($configs) == $cartNum) {
                $services[$logisticsId] = [
                    'id' => $logisticsId,
                    'name' => $logistics->getName($logisticsId),
                    'fee' => $this->getFeeByLogisticsConfigs($configs),
                ];
            }
        }

        // 3. 设置默认运费,或者将默认快递移到底部
        if (!$services) {
            $services[1] = $this->defaultService;
        } else {
            $defaultService = $services[1];
            unset($services[1]);
            $services[1] = $defaultService;
        }

        return array_values($services);
    }

    /**
     * Repo: 根据物流配置计算运费
     *
     * @param array $configs 包括cart, rule两个键
     * @return int
     */
    protected function getFeeByLogisticsConfigs(array $configs)
    {
        // 1. 计算首费
        // http://service.taobao.com/support/knowledge-1118327.htm
        $bestRule = null;
        $startFee = 0;

        foreach ($configs as $config) {
            /** @var $rule ShippingTplRule */
            $rule = $config['rule'];

            //  按“取最大首费，最小增费”作为首费来计算
            if ($rule['startFee'] > $startFee
                || ($rule['startFee'] == $startFee && $rule['plusFee'] < $bestRule['plusFee'])
            ) {
                $startFee = $rule['startFee'];
                $bestRule = $rule;
            }
        }

        // 2. 计算剩下的费用
        $fee = $startFee;
        foreach ($configs as $config) {
            $quantity = $config['cart']['quantity'];
            // 如果运费模板规则用在首费,减去首费的一件商品,并且只用1次
            if ($bestRule === $config['rule']) {
                $quantity = $config['cart']['quantity'] - 1;
                $bestRule = null;
            }
            $fee += $quantity * $config['rule']['plusFee'];
        }

        return sprintf('%.2f', $fee);
    }

    public function afterFind()
    {
        parent::afterFind();
        $this['logisticsIds'] = explode(',', $this['logisticsIds']);
        $this['useLogisticsIds'] = (array) json_decode($this['useLogisticsIds'], true);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['logisticsIds'] = implode(',', (array) $this['logisticsIds']);
        // key是原来的物流服务商编号,value是要使用的物流商编号
        $this['useLogisticsIds'] = json_encode((array) $this['useLogisticsIds'], \JSON_FORCE_OBJECT);
    }

    public function afterSave()
    {
        parent::afterSave();
        $this['logisticsIds'] = explode(',', $this['logisticsIds']);
        $this['useLogisticsIds'] = (array) json_decode($this['useLogisticsIds'], true);
    }
}
