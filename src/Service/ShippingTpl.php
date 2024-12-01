<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Cart\Service\Cart;

class ShippingTpl extends \Miaoxing\Plugin\BaseService
{
    public const CUSTOM_RULE = 0;

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
     * @phpstan-ignore-next-line
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
        /** @phpstan-ignore-next-line */
        $data = parent::toArray($returnFields);
        // @phpstan-ignore-next-line
        if (!$this->isColl) {
            $data['rules'] = $this->getRules()->toArray();
        }

        return $data;
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
     * @param Cart|Cart[] $carts
     * @param \Miaoxing\Address\Service\Address $address
     * @return array
     * @phpstan-ignore-next-line
     */
    public function getShippingServices(Cart $carts, ?\Miaoxing\Address\Service\Address $address = null)
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
            if (
                $rule['startFee'] > $startFee
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
        $this['logisticsIds'] = explode(',', $this['logisticsIds']);
        $this['useLogisticsIds'] = (array) json_decode($this['useLogisticsIds'], true);
    }

    public function beforeSave()
    {
        $this['logisticsIds'] = implode(',', (array) $this['logisticsIds']);
        // key是原来的物流服务商编号,value是要使用的物流商编号
        $this['useLogisticsIds'] = json_encode((array) $this['useLogisticsIds'], \JSON_FORCE_OBJECT);
    }

    public function afterSave()
    {
        $this['logisticsIds'] = explode(',', $this['logisticsIds']);
        $this['useLogisticsIds'] = (array) json_decode($this['useLogisticsIds'], true);
    }
}
