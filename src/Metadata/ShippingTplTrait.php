<?php

namespace Miaoxing\Logistics\Metadata;

/**
 * @property int $id
 * @property int $appId
 * @property string $serviceIds 支持的物流服务编号
 * @property string $name 名称
 * @property int $sendRegionId 发货地址
 * @property int $sendTime 发货时间。以小时为单位，-1表示无
 * @property bool $isFreeShipping 是否包邮
 * @property int $valuationType 计价方式。1:按件;2:按重量
 * @property int $sort 顺序
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property string|null $deletedAt
 * @property int $deletedBy
 * @internal will change in the future
 */
trait ShippingTplTrait
{
}
