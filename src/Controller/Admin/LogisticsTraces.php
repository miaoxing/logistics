<?php

namespace Miaoxing\Logistics\Controller\Admin;

class LogisticsTraces extends \Miaoxing\Plugin\BaseController
{
    protected $controllerName = '物流跟踪';

    protected $actionPermissions = [
        'index,show' => '查询',
    ];

    protected $adminNavId = 'order';

    public function indexAction($req)
    {
        $this->page->setTitle('物流跟踪');

        return get_defined_vars();
    }

    public function showAction($req)
    {
        if (!$req['id']) {
            return $this->err('缺少ID');
        }

        $logisticsId = explode('_', $req['id'])[0] ?: $req['logisticsId'];
        $logisticsNo = explode('_', $req['id'])[1] ?: $req['logisticsNo'];

        $logistics = wei()->logistics()->findOneById($logisticsId);
        $traces = $logistics->getTraces($logisticsNo);
        if (isset($traces['next'])) {
            $ret = [
                'data' => [
                    'url' => $traces['next'],
                ],
                'code' => 1,
                'message' => '操作成功',
            ];
        } else {
            $ret = [
                'data' => [
                    'logistics' => $logistics->toArray() + [
                            'no' => $logisticsNo,
                        ],
                    'traces' => $traces['traces'],
                ],
                'code' => 1,
                'message' => '操作成功',
            ];
        }

        return $this->ret($ret);
    }
}
