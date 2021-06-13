<?php

namespace Miaoxing\Logistics\Resource;

use Miaoxing\Logistics\Service\LogisticsServiceModel;
use Miaoxing\Plugin\Resource\BaseResource;

class LogisticsServiceResource extends BaseResource
{
    public function transform(LogisticsServiceModel $service): array
    {
        return [
            'name' => $service->name,
        ];
    }
}
