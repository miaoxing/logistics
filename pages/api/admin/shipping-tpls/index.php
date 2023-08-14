<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BasePage;
use Miaoxing\Services\Page\CollTrait;
use Miaoxing\Services\Page\PostToPatchTrait;
use Miaoxing\Services\Service\IndexAction;

return new class () extends BasePage {
    use CollTrait;
    use PostToPatchTrait {
        PostToPatchTrait::post insteadof CollTrait;
    }

    public function get()
    {
        return IndexAction::new()
            ->beforeFind(function (ShippingTplModel $models) {
                $models->setDefaultSortColumn(['sort', 'id']);
            })
            ->exec($this);
    }
};
