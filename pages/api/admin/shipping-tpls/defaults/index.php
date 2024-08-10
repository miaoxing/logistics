<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BasePage;

return new class extends BasePage {
    public function get()
    {
        $data = array_merge(ShippingTplModel::toArray(), [
            'rules' => [],
        ]);

        return suc(['data' => $data]);
    }
};
