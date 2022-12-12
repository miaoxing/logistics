<?php

use Miaoxing\Logistics\Service\LogisticsAddressModel;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Page\ItemTrait;
use Miaoxing\Services\Service\ShowAction;
use Miaoxing\Services\Service\UpdateAction;
use Wei\V;

return new class () extends BaseController {
    use ItemTrait;

    public function get()
    {
        return ShowAction
            ::afterFind(function (LogisticsAddressModel $address) {
                $region = $address->region;
                while ($region && $region->parentId) {
                    $region = $region->parent;
                }
            })
            ->exec($this);
    }

    public function patch()
    {
        return UpdateAction::new()
            ->beforeSave(function (LogisticsAddressModel $address, $req) {
                $v = V::defaultOptional();
                $v->char('name', '联系人姓名', 0, 16)->required($address->isNew())->notBlank();
                $v->char('phone', '联系人电话', 0, 16)->required($address->isNew())->notBlank();
                $v->uDefaultInt('regionId', '地区')->required($address->isNew())->notBlank();
                $v->tinyChar('address', '详细地址')->required($address->isNew())->notBlank();
                $v->char('postalCode', '邮编', 0, 8);
                $v->array('types', '使用场景', 0, 8)->unique()->each(function (V $v) {
                    $v->self()->label('值')->inConst(LogisticsAddressModel::class, 'TYPE');
                });
                $v->tinyChar('remark', '备注');
                $v->uSmallInt('sort', '顺序');
                return $v->check($req);
            })
            ->exec($this);
    }
};
