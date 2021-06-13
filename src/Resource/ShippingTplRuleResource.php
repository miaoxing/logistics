<?php

namespace Miaoxing\Logistics\Resource;

use Miaoxing\Logistics\Service\ShippingTplRuleModel;
use Miaoxing\Plugin\Resource\BaseResource;

class ShippingTplRuleResource extends BaseResource
{
    public function transform(ShippingTplRuleModel $rule): array
    {
        return [
            $this->extract($rule, [
                'isDefault',
                'regionIds',
                'startAmount',
                'startFee',
                'addAmount',
                'addFee',
            ]),
            'service' => LogisticsServiceResource::whenLoaded($rule, 'service'),
        ];
    }
}
