<?php

namespace Miaoxing\Logistics\Seeder;

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Logistics\Service\ShippingTplRuleModel;
use Miaoxing\Plugin\Seeder\BaseSeeder;

class V20210415002226CreateLogistics extends BaseSeeder
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        ShippingTplModel::saveAttributes([
            'serviceIds' => '1',
            'name' => '包邮',
            'isFreeShipping' => true,
        ]);

        $shippingTpl = ShippingTplModel::saveAttributes([
            'serviceIds' => '1',
            'name' => '按件 首件10 加件8',
            'isFreeShipping' => false,
        ]);

        ShippingTplRuleModel::save([
            'shippingTplId' => $shippingTpl->id,
            'isDefault' => true,
            'startAmount' => 1,
            'startFee' => 10,
            'addAmount' => 1,
            'addFee' => 10,
        ]);
    }
}
