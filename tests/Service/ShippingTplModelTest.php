<?php

namespace MiaoxingTest\Logistics\Service;

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Logistics\Service\ShippingTplRuleModel;
use Miaoxing\Plugin\Test\BaseTestCase;

class ShippingTplModelTest extends BaseTestCase
{
    protected function getFreeShippingTpl()
    {
        return ShippingTplModel::fromArray([
            'isFreeShipping' => true,
        ]);
    }

    protected function getShippingTpl()
    {
        $tpl = ShippingTplModel::fromArray([
            'name' => '测试',
        ]);

        $rules = ShippingTplRuleModel::newColl();
        $tpl->setRelationValue('rules', $rules);

        // 默认快递
        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl->id,
            'serviceId' => 1,
            'isDefault' => true,
            'startFee' => 11,
            'plusFee' => 3,
        ]);

        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl->id,
            'serviceId' => 1,
            'isDefault' => false,
            'regionIds' => [
                440300,
                440100, // 深圳市,广州市
            ],
            'startFee' => 8,
            'plusFee' => 1,
        ]);

        // 物流服务2
        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => 2,
            'isDefault' => true,
            'startFee' => 10,
            'plusFee' => 2,
        ]);

        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => 2,
            'isDefault' => false,
            'regionIds' => [
                440100, // 广州市
            ],
            'startFee' => 9,
            'plusFee' => 1,
        ]);

        return $tpl;
    }

    protected function getShippingTpl2()
    {
        $tpl = ShippingTplModel::fromArray([
            'name' => '测试',
        ]);

        $rules = ShippingTplRuleModel::newColl();
        $tpl->setRelationValue('rules', $rules);

        // 默认快递
        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => '1',
            'isDefault' => '1',
            'startFee' => '10.00',
            'plusFee' => '3.00',
        ]);

        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => '1',
            'isDefault' => '0',
            'regionIds' => [
                440300,
                440100, // 深圳市,广州市
            ],
            'startFee' => '8.00',
            'plusFee' => '1.00',
        ]);

        // 物流2
        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => '2',
            'isDefault' => true,
            'startFee' => '10.02',
            'plusFee' => '2.02',
        ]);

        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => '2',
            'isDefault' => '0',
            'regionIds' => [
                440100,
                310100, // 广州市,上海市市辖区
            ],
            'startFee' => '9.02',
            'plusFee' => '1.02',
        ]);

        // 物流3
        $rules[] = ShippingTplRuleModel::fromArray([
            'shippingTplId' => $tpl['id'],
            'serviceId' => '3',
            'isDefault' => '1',
            'startFee' => '10.03',
            'plusFee' => '2.03',
        ]);

        return $tpl;
    }

    public function testGetRulesByCity()
    {
        $tpl = $this->getShippingTpl();

        $rules = $tpl->getRulesByCity('深圳市');

        $this->assertEquals(2, $rules->count());

        // 匹配第一条
        $this->assertSame('8', $rules[0]->startFee);

        // 匹配第二条
        $this->assertSame('10', $rules[1]->startFee);
    }

    public function testGetRulesBySpecialCity()
    {
        $tpl = $this->getShippingTpl2();

        $rules = $tpl->getRulesByCity('上海市');

        $this->assertEquals(3, $rules->count());

        // 匹配第一条
        $this->assertSame('10', $rules[0]->startFee);

        // 匹配第二条
        $this->assertSame('9.02', $rules[1]->startFee);

        // 匹配第三条
        $this->assertSame('10.03', $rules[2]->startFee);
    }

    public function testGetRulesBySpecialCityId()
    {
        $tpl = $this->getShippingTpl2();

        $rules = $tpl->getRulesByCity(310100);

        $this->assertEquals(3, $rules->count());

        // 匹配第一条
        $this->assertSame('10', $rules[0]->startFee);

        // 匹配第二条
        $this->assertSame('9.02', $rules[1]->startFee);

        // 匹配第三条
        $this->assertSame('10.03', $rules[2]->startFee);
    }

    /**
     * 测试没有城市时,返回默认规则
     */
    public function testGetRulesByCityWithEmptyCity()
    {
        $tpl = $this->getShippingTpl();

        $rules = $tpl->getRulesByCity();

        $this->assertEquals(2, $rules->count());

        $this->assertTrue($rules[0]->isDefault);

        $this->assertTrue($rules[1]->isDefault);
    }
}
