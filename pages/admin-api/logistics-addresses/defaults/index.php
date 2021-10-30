<?php

use Miaoxing\Logistics\Service\LogisticsAddressModel;
use Miaoxing\Plugin\BaseController;

return new class () extends BaseController {
    public function get()
    {
        return LogisticsAddressModel::toRet();
    }
};
