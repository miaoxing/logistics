<?php

namespace Miaoxing\Logistics\Controller\Admin;

use Miaoxing\Logistics\Service\ShippingTpl;

class ShippingTpls extends \miaoxing\plugin\BaseController
{
    protected $controllerName = '运费模板管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
    ];

    /**
     * @todo 实现后台无权限action
     */
    protected $guestPages = [
        'admin/ShippingTpls/regions',
    ];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $shippingTpls = wei()->shippingTpl()->curApp();

                $shippingTpls
                    ->notDeleted()
                    ->desc('id')
                    ->findAll();

                return $this->suc([
                    'message' => '读取列表成功',
                    'data' => $shippingTpls->toArray(),
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $shippingTpls->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function createAction($req)
    {
        return $this->updateAction($req);
    }

    public function editAction($req)
    {
        $shippingTpl = wei()->shippingTpl()->curApp()->findId($req['id']);

        $logistics = wei()->logistics()->getNames();

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $shippingTpl = wei()->shippingTpl()->curApp()->findId($req['id']);

        $logistics = wei()->logistics()->findAll();
        $logisticsIds = $logistics->getAll('id');
        $useLogisticsIds = array_merge($logisticsIds, [ShippingTpl::CUSTOM_RULE]);

        // 验证模板参数是否正确
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'name' => [],
                'freeShipping' => [
                    'in' => [
                        'array' => [0, 1],
                    ],
                ],
                'logisticsIds' => [
                    'type' => 'array',
                    'all' => [
                        'rules' => [
                            'in' => [
                                'strict' => true,
                                'array' => $logisticsIds,
                            ],
                        ],
                    ],
                ],
                'useLogisticsIds' => [
                    'type' => 'array',
                    'all' => [
                        'rules' => [
                            'in' => [
                                'array' => $useLogisticsIds,
                            ],
                        ],
                    ],
                ],
                'rules' => [
                    'required' => !$req['freeShipping'],
                    'type' => 'array',
                ],
            ],
            'names' => [
                'name' => '模板名称',
                'freeShipping' => '是否包邮',
                'logisticsIds' => '物流服务商',
                'useLogisticsIds' => '物流服务商运费模板',
                'rules' => '运费规则',
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getJoinedMessage());
        }

        $shippingTpl->save($req);

        $rules = $shippingTpl->getRules();
        $rules->saveColl($req['rules'], ['shippingTplId' => $shippingTpl['id']]);

        return $this->suc();
    }

    public function destroyAction($req)
    {
        $shippingTpl = wei()->shippingTpl()->curApp()->findOneById($req['id']);

        // 检查运费模板是否被商品使用
        $products = wei()->product()->notDeleted()->findAll(['shippingTplId' => $req['id']]);
        if ($products->length()) {
            return $this->err(sprintf('很抱歉,该模板已被商品"%s"使用,不能删除', implode('，', $products->getAll('name'))));
        }

        $shippingTpl->softDelete();

        return $this->suc();
    }

    public function regionsAction()
    {
        // 1. 找出所有的省份
        $provinces = wei()->appDb('areas')
            ->select('id, name')
            ->asc('id')
            ->fetchAll(['parentId' => 0]);

        $ids = array_column($provinces, 'id');
        $ids = array_map('intval', $ids);
        $provinces = array_column($provinces, 'name', 'id');

        // 2. 找出所有的城市
        $dbCities = wei()->appDb('areas')
            ->select('id, parentId, name')
            ->fetchAll(['parentId' => $ids]);

        $cities = [];
        foreach ($dbCities as $city) {
            $cities[$provinces[$city['parentId']]][$city['id']] = $city['name'];
        }

        // 3. 定义区域数据
        $areas = [[
            'name' => '华东',
            'provinces' => ['上海市', '江苏省', '浙江省', '安徽省', '江西省'],
        ], [
            'name' => '华北',
            'provinces' => ['北京市', '天津市', '山西省', '山东省', '河北省', '内蒙古自治区'],
        ], [
            'name' => '华中',
            'provinces' => ['湖南省', '湖北省', '河南省'],
        ], [
            'name' => '华南',
            'provinces' => ['广东省', '广西壮族自治区', '福建省', '海南省'],
        ], [
            'name' => '东北',
            'provinces' => ['辽宁省', '吉林省', '黑龙江省'],
        ], [
            'name' => '西北',
            'provinces' => ['陕西省', '新疆维吾尔自治区', '甘肃省', '宁夏回族自治区', '青海省'],
        ], [
            'name' => '西南',
            'provinces' => ['重庆市', '云南省', '贵州省', '西藏自治区', '四川省'],
        ], [
            'name' => '港澳台',
            'provinces' => ['香港特别行政区', '澳门特别行政区', '台湾省'],
        ]];

        return $this->suc([
            'areas' => $areas,
            'provinces' => $provinces,
            'cities' => $cities,
        ]);
    }
}
