<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Page\ItemTrait;
use Miaoxing\Services\Service\UpdateAction;
use Wei\Req;
use Wei\V;

return new class extends BaseController {
    use ItemTrait;

    protected $expand = [
        'rules',
    ];

    public function patch()
    {
        return UpdateAction
            ::beforeSave(function (ShippingTplModel $shippingTpl, $req) {
                $shippingTpl->serviceIds = array_filter(array_unique(array_column((array) $req['rules'], 'serviceId')));

                return V::defaultOptional()
                    ->tinyChar('name', '名称')->required($shippingTpl->isNew())->notBlank()
                    ->bool('isFreeShipping', '是否包邮')
                    ->string('valuationType', '计价方式')->in([
                        [
                            ShippingTplModel::VALUATION_TYPE_BY_PIECE,
                            ShippingTplModel::VALUATION_TYPE_BY_WEIGHT,
                        ],
                    ])
                    ->array('rules', '运费规则')->required($this->isRulesRequired($shippingTpl))
                    ->each(function (V $v) {
                        $isDefault = $v->getData()['isDefault'] ?? false;
                        $v
                            ->uDefaultInt('serviceId', '服务编号')
                            ->array('regionIds', '地区', $isDefault ? null : 1)->required(!$isDefault)
                            ->uSmallInt('startAmount', '首费数量', 1)
                            ->uNumber('startFee', '首费金额', 10, 2)
                            ->uSmallInt('addAmount', '增费数量', 1)
                            ->uNumber('addFee', '增费金额', 10, 2);
                    })
                    ->uSmallInt('sort', '顺序')
                    ->check($req);
            })
            ->afterSave(function (ShippingTplModel $shippingTpl, $req) {
                if ($req['rules']) {
                    $shippingTpl->rules()->saveRelation($req['rules']);
                }
            })
            ->exec($this);
    }

    private function isRulesRequired(ShippingTplModel $shippingTpl): bool
    {
        // 包邮时总是可以不传规则
        if ($shippingTpl->isFreeShipping) {
            return false;
        }

        // 编辑时可以不传
        return $shippingTpl->isNew();
    }
};
