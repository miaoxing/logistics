<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;
use Miaoxing\Region\Service\RegionModel;
use Wei\Model\SoftDeleteTrait;

/**
 * @property ShippingTplRuleModel|ShippingTplRuleModel[] $rules
 * @property string|null $id
 * @property string $appId
 * @property array $serviceIds 支持的物流服务编号
 * @property string $name 名称
 * @property int $sendRegionId 发货地址
 * @property int $sendTime 发货时间。以小时为单位，-1表示无
 * @property bool $isFreeShipping 是否包邮
 * @property int $valuationType 计价方式。1:按件;2:按重量
 * @property int $sort 顺序
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 * @property ShippingTplRuleModel|ShippingTplRuleModel[] $getRulesByCity 根据城市编号或名称,获取匹配的运费规则
 */
class ShippingTplModel extends BaseModel
{
    use HasAppIdTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use SnowflakeTrait;
    use SoftDeleteTrait;

    public const VALUATION_TYPE_BY_PIECE = 1;

    public const VALUATION_TYPE_BY_WEIGHT = 2;

    protected $columns = [
        'serviceIds' => [
            'cast' => [
                'list',
                'type' => 'int',
            ],
        ],
    ];

    /**
     * 特殊城市与所属"市辖区"ID的对应关系
     *
     * @var array
     * @internal
     */
    protected $cityIds = [
        '北京市' => 110100,
        '天津市' => 120100,
        '上海市' => 310100,
        '重庆市' => 500100,
    ];

    /**
     * @Relation
     */
    public function rules(): ShippingTplRuleModel
    {
        return $this->hasMany(ShippingTplRuleModel::class);
    }

    /**
     * 获取城市和匹配的运费规则
     *
     * @param string|int $city 如果为空，则按需识别出城市
     * @return array 返回数组包含 city 和 rules
     */
    public function getCityAndRules($city = null): array
    {
        if ($this->isFreeShipping) {
            // 包邮无需加载规则
            $rules = [];
        } elseif (1 === $this->rules->count()) {
            // 只有默认规则，直接返回
            $rules = $this->rules;
        } else {
            // 定位城市并根据城市过滤出匹配的规则
            $city || $city = wei()->lbs->getIpInfo()['city'];
            $rules = $this->getRulesByCity($city);
        }

        return [
            'city' => $city,
            'rules' => $rules,
        ];
    }

    /**
     * 根据城市编号或名称,获取匹配的运费规则
     *
     * @param int|string $cityId 城市编号或名称,如上海市辖区310000,深圳440300
     * @return ShippingTplRuleModel|ShippingTplRuleModel[]
     */
    public function getRulesByCity($cityId = null): ShippingTplRuleModel
    {
        // 如果不是数字ID,查表转换为数字ID
        if ($cityId && !is_numeric($cityId)) {
            // 如果是特殊城市,如上海市,转换为上海市"市辖区"的ID
            if (isset($this->cityIds[$cityId])) {
                $cityId = $this->cityIds[$cityId];
            } else {
                $region = RegionModel::select('id')->findBy('name', $cityId);
                if ($region) {
                    $cityId = $region['id'];
                }
            }
        }

        $serviceRules = [];
        foreach ($this->rules as $rule) {
            $serviceRules[$rule->serviceId][] = $rule;
        }

        $matches = [];
        $default = null;
        foreach ($serviceRules as $serviceId => $rules) {
            foreach ($rules as $rule) {
                if ($rule->isDefault) {
                    $default = $rule;
                }
                if (in_array($cityId, $rule->regionIds, true)) {
                    $matches[$serviceId] = $rule;
                    break;
                }
            }
            // 没有匹配的地区,使用默认
            if (!isset($matches[$serviceId])) {
                $matches[$serviceId] = $default;
            }
        }

        return ShippingTplRuleModel::newColl(array_values($matches));
    }
}
