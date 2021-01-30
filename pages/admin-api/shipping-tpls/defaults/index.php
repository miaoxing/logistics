<?php

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\BaseController;

return new class extends BaseController {
    public function get()
    {
        $data = array_merge(ShippingTplModel::toArray(), [
            'rules' => [],
        ]);

        return suc(['data' => $data]);
    }
};
