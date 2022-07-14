<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Page\ItemTrait;
use Miaoxing\Services\Service\UpdateAction;
use Wei\V;

return new class () extends BaseController {
    use ItemTrait;

    protected $include = [
        'rules',
    ];

    public function patch()
    {
        return UpdateAction::new()
            ->beforeSave(function (ShippingTplModel $shippingTpl, $req) {
                $shippingTpl->serviceIds = array_filter(array_unique(array_column((array) $req['rules'], 'serviceId')));

                $v = V::defaultOptional();
                $v->tinyChar('name', '名称')->required($shippingTpl->isNew())->notBlank();
                $v->bool('isFreeShipping', '是否包邮');
                $v->string('valuationType', '计价方式')->in([
                    [
                        ShippingTplModel::VALUATION_TYPE_BY_PIECE,
                        ShippingTplModel::VALUATION_TYPE_BY_WEIGHT,
                    ],
                ]);
                $v->array('rules', '运费规则')->required($this->isRulesRequired($shippingTpl))->each(function (V $v) {
                    $isDefault = $v->getData()['isDefault'] ?? false;
                    $v->uDefaultInt('serviceId', '服务编号');
                    $v->array('regionIds', '地区', $isDefault ? null : 1)->required(!$isDefault);
                    $v->uSmallInt('startAmount', '首费数量', 1);
                    $v->uNumber('startFee', '首费金额', 10, 2);
                    $v->uSmallInt('addAmount', '增费数量', 1);
                    $v->uNumber('addFee', '增费金额', 10, 2);
                });
                $v->uSmallInt('sort', '顺序');
                return $v->check($req);
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
