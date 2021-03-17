<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\ShippingTplTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * @property ShippingTplRuleModel|ShippingTplRuleModel[] $rules
 */
class ShippingTplModel extends BaseModel
{
    use HasAppIdTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use ShippingTplTrait;
    use SoftDeleteTrait;

    const VALUATION_TYPE_BY_PIECE = 1;

    const VALUATION_TYPE_BY_WEIGHT = 2;

    protected $columns = [
        'serviceIds' => [
            'cast' => [
                'list',
                'type' => 'int',
            ],
        ],
    ];

    /**
     * @Relation
     */
    public function rules()
    {
        return $this->hasMany(ShippingTplRuleModel::class);
    }
}
