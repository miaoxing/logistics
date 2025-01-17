<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;
use Miaoxing\Region\Service\RegionModel;
use Wei\Model\SoftDeleteTrait;

/**
 * @property string|null $id
 * @property string $appId 应用编号
 * @property int $regionId 地区编号
 * @property string $name 联系人姓名
 * @property string $phone 联系人电话
 * @property string $address 详细地址
 * @property string $postalCode 邮编
 * @property array $types 使用场景
 * @property string $remark 备注
 * @property int $sort 顺序
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 * @property RegionModel $region
 * @property string|null $id
 * @property string $appId 应用编号
 * @property int $regionId 地区编号
 * @property string $name 联系人姓名
 * @property string $phone 联系人电话
 * @property string $address 详细地址
 * @property string $postalCode 邮编
 * @property array $types 使用场景
 * @property string $remark 备注
 * @property int $sort 顺序
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 */
class LogisticsAddressModel extends BaseModel
{
    use ConstTrait;
    use HasAppIdTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use SnowflakeTrait;
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

    public function region(): RegionModel
    {
        return $this->belongsTo(RegionModel::class);
    }
}
