<?php

use Miaoxing\Logistics\Service\LogisticsAddressModel;
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
            ->beforeFind(static function (LogisticsAddressModel $models) {
                $models->setDefaultSortColumn(['sort', 'id']);
            })
            ->afterFind(static function (LogisticsAddressModel $models) {
                $models->load('region.parent.parent');
            })
            ->exec($this);
    }
};
