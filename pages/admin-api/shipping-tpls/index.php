<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Page\CollTrait;
use Miaoxing\Services\Page\PostToPatchTrait;
use Miaoxing\Services\Service\IndexAction;

return new class extends BaseController {
    use CollTrait;
    use PostToPatchTrait {
        PostToPatchTrait::post insteadof CollTrait;

    }

    public function get()
    {
        return IndexAction
            ::beforeFind(function (ShippingTplModel $models) {
                $models->setDefaultSortColumn(['sort', 'id']);
            })
            ->exec($this);
    }
};
