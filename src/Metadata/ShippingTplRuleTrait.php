<?php

namespace Miaoxing\Logistics\Metadata;

/**
 * @property int|null $id
 * @property int $appId
 * @property int $shippingTplId
 * @property int $serviceId 物流服务编号
 * @property bool $isDefault 是否默认
 * @property string $regionIds 指定的区域编号
 * @property int $startAmount 首费数量
 * @property string $startFee 首费金额
 * @property int $addAmount 增费数量
 * @property string $addFee 增费金额
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property string|null $deletedAt
 * @property int $deletedBy
 * @internal will change in the future
 */
trait ShippingTplRuleTrait
{
}
