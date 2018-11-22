<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Config\ConfigTrait;

/**
 * 物流服务商
 *
 * 注: 以s结尾才是名词
 *
 * @property int defaultLogisticsId
 */
class Logistics extends \Miaoxing\Plugin\BaseModel
{
    use ConfigTrait;

    protected $configs = [
        'defaultLogisticsId' => [
            'default' => 0,
        ],
    ];

    /**
     * 到店自提
     */
    const ID_SELF_PICKUP = 2;

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'db' => 'app.db',
    ];

    /**
     * 物流公司编号和名称的对应数组
     *
     * @var array
     */
    protected $names;

    /**
     * Repo: 获取物流公司编号和名称的对应数组
     *
     * @return array
     */
    public function getNames()
    {
        if (!$this->names) {
            $this->names = $this->cache->get('logisticsName', 60, function () {
                $names = [];
                $logistics = wei()->logistics()->select('id, name')->fetchAll();
                foreach ($logistics as $row) {
                    $names[$row['id']] = $row['name'];
                }

                return $names;
            });
        }

        return $this->names;
    }

    /**
     * Repo: 根据编号获取物流服务商的名称
     *
     * @param int $id
     * @return string|null
     */
    public function getName($id)
    {
        $names = $this->getNames();

        return isset($names[$id]) ? $names[$id] : null;
    }

    /**
     * Repo: 根据编号获取物流服务商的名称
     *
     * @param $name
     * @return string
     */
    public function getKeyByName($name)
    {
        $names = $this->getNames();
        $key = array_search($name, $names);

        return $key ?: false;
    }

    /**
     * 获取物流跟踪信息
     *
     * @param string $logisticsNo
     * @return array
     */
    public function getTraces($logisticsNo)
    {
        // 1. 触发获取前事件,允许外部根据特定的物流,获取物流信息
        $ret = $this->event->trigger('preLogisticsGetTraces', [$this, $logisticsNo]);
        if ($ret) {
            return $ret;
        }

        // 2. 尝试通过接口调用
        if ($this['kuaidi100Id']) {
            $ret = wei()->kuaidi100->getTraces($this['kuaidi100Id'], $logisticsNo);
            if ($ret['code'] === 1) {
                return $ret;
            }
        }

        // 3. 如果支持快递100,跳转到快递100展示
        if ($this['kuaidi100Id']) {
            $url = $this->url->append('https://m.kuaidi100.com/index_all.html', [
                'type' => $this['kuaidi100Id'],
                'postid' => $logisticsNo,
            ]);

            return $this->postGetTraces(['code' => 1, 'message' => '获取成功', 'next' => $url]);
        }

        // 4. 默认返回空数据
        return $this->postGetTraces(['code' => 1, 'message' => '获取成功', 'traces' => []]);
    }

    /**
     * 触发获取物流跟踪信息事件并返回
     *
     * @param array $ret
     * @return array
     */
    protected function postGetTraces($ret)
    {
        $this->event->trigger('postLogisticsGetTraces', [&$ret]);

        return $ret;
    }
}
