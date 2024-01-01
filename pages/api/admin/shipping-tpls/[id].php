<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BasePage;
use Miaoxing\Services\Page\ItemTrait;
use Miaoxing\Services\Service\UpdateAction;
use Wei\Req;
use Wei\V;

return new class () extends BasePage {
    use ItemTrait;

    protected $include = [
        'rules',
    ];

    public function patch()
    {
        return UpdateAction::new()
            ->validate(function (ShippingTplModel $shippingTpl, $req) {
                $v = V::defaultOptional();
                $v->setModel($shippingTpl);
                $v->modelColumn('name', '名称')->required($shippingTpl->isNew())->notBlank();
                $v->modelColumn('isFreeShipping', '是否包邮');
                $v->inConst('valuationType', '计价方式', ShippingTplModel::class, 'VALUATION_TYPE');
                $v->array('rules', '运费规则')->required($this->isRulesRequired($shippingTpl, $req))->each(static function (V $v) {
                    $isDefault = $v->getData()['isDefault'] ?? false;
                    $v->uDefaultInt('serviceId', '服务编号');
                    $v->array('regionIds', '地区', $isDefault ? null : 1)->required(!$isDefault);
                    $v->uSmallInt('startAmount', '首费数量', 1);
                    $v->uNumber('startFee', '首费金额', 10, 2);
                    $v->uSmallInt('addAmount', '增费数量', 1);
                    $v->uNumber('addFee', '增费金额', 10, 2);
                });
                $v->modelColumn('sort', '顺序');
                return $v->check($req);
            })
            ->beforeSave(static function (ShippingTplModel $shippingTpl, $req) {
                $shippingTpl->serviceIds = array_filter(array_unique(array_column((array) $req['rules'], 'serviceId')));
            })
            ->afterSave(static function (ShippingTplModel $shippingTpl, $req) {
                if ($req['rules']) {
                    $shippingTpl->rules()->saveRelation($req['rules']);
                }
            })
            ->exec($this);
    }

    private function isRulesRequired(ShippingTplModel $shippingTpl, Req $req): bool
    {
        // 包邮时总是可以不传规则
        if ($req['isFreeShipping']) {
            return false;
        }

        // 编辑时可以不传
        return $shippingTpl->isNew();
    }
};
