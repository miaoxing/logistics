<?php

namespace Miaoxing\Logistics\Resource;

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\Resource\BaseResource;

class ShippingTplResource extends BaseResource
{
    public function transform(ShippingTplModel $shippingTpl): array
    {
        return [
            $this->extract($shippingTpl, [
                'id',
                'name',
                'isFreeShipping',
                'valuationType',
            ]),
            'rules' => ShippingTplRuleResource::whenLoaded($shippingTpl, 'rules'),
        ];
    }
}
