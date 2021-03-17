<?php

use Miaoxing\Logistics\Service\LogisticsAddressModel;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Page\ItemTrait;
use Miaoxing\Services\Service\ShowAction;
use Miaoxing\Services\Service\UpdateAction;
use Wei\V;

return new class extends BaseController {
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
        return UpdateAction
            ::beforeSave(function (LogisticsAddressModel $address, $req) {
                return V::defaultOptional()
                    ->char('name', '联系人姓名', 0, 16)->required($address->isNew())->notBlank()
                    ->char('phone', '联系人电话', 0, 16)->required($address->isNew())->notBlank()
                    ->uDefaultInt('regionId', '地区')->required($address->isNew())->notBlank()
                    ->tinyChar('address', '详细地址')->required($address->isNew())->notBlank()
                    ->char('postalCode', '邮编', 0, 8)
                    ->array('type', '使用场景')
                    ->tinyChar('remark', '备注')
                    ->uSmallInt('sort', '顺序')
                    ->check($req);
            })
            ->exec($this);
    }
};
