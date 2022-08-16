<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\ShippingTplRuleTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;
use Wei\Model\SoftDeleteTrait;

/**
 * @property LogisticsServiceModel $service
 */
class ShippingTplRuleModel extends BaseModel
{
    use ModelTrait;
    use ShippingTplRuleTrait;
    use SnowflakeTrait;
    use SoftDeleteTrait;

    protected $columns = [
        'regionIds' => [
            'cast' => [
                'list',
                'type' => 'int',
            ],
        ],
    ];

    public function service(): LogisticsServiceModel
    {
        return $this->belongsTo(LogisticsServiceModel::class, 'id', 'serviceId');
    }
}
