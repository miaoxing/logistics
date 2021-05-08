<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\LogisticsAddressTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Region\Service\RegionModel;

/**
 * @property RegionModel $region
 */
class LogisticsAddressModel extends BaseModel
{
    use LogisticsAddressTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use SoftDeleteTrait;

    /**
     * @internal
     */
    public const TYPE_RETURN = 1;

    /**
     * @internal
     */
    protected $typeNames = [
        self::TYPE_RETURN => '退货',
    ];

    protected $columns = [
        'types' => [
            'cast' => [
                'list',
                'type' => 'int',
            ],
        ],
    ];

    public function region()
    {
        return $this->belongsTo(RegionModel::class);
    }
}
