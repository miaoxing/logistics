<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\ShippingTplRuleTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

class ShippingTplRuleModel extends BaseModel
{
    use ModelTrait;
    use ShippingTplRuleTrait;
    use SoftDeleteTrait;

    protected $columns = [
        'regionIds' => [
            'cast' => [
                'list',
                'type' => 'int',
            ],
        ],
    ];
}
