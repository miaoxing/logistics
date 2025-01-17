<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;
use Wei\Model\SoftDeleteTrait;

/**
 * @property string|null $id
 * @property string $appId
 * @property string $shippingTplId
 * @property int $serviceId 物流服务编号
 * @property bool $isDefault 是否默认
 * @property array $regionIds 指定的区域编号
 * @property int $startAmount 首费数量
 * @property string $startFee 首费金额
 * @property int $addAmount 增费数量
 * @property string $addFee 增费金额
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 * @property LogisticsServiceModel $service
 * @property string|null $id
 * @property string $appId
 * @property string $shippingTplId
 * @property int $serviceId 物流服务编号
 * @property bool $isDefault 是否默认
 * @property array $regionIds 指定的区域编号
 * @property int $startAmount 首费数量
 * @property string $startFee 首费金额
 * @property int $addAmount 增费数量
 * @property string $addFee 增费金额
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 */
class ShippingTplRuleModel extends BaseModel
{
    use ModelTrait;
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
