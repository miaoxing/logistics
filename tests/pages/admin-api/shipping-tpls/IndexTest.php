<?php

namespace MiaoxingTest\Logistics\Pages\AdminApi\Categories;

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;

class IndexTest extends BaseTestCase
{
    public function testGet()
    {
        User::loginById(1);

        $maxSort = ShippingTplModel::max('sort');

        $shippingTpl = ShippingTplModel::save([
            'name' => '测试运费模板' . ($maxSort + 1),
            'sort' => $maxSort + 1,
        ]);
        $shippingTpl2 = ShippingTplModel::save([
            'name' => '测试运费模板' . ($maxSort + 2),
            'sort' => $maxSort + 2,
        ]);

        $ret = Tester::request(['limit' => 2])->getAdminApi('shipping-tpls');

        $this->assertSame($shippingTpl2->toArray(), $ret['data'][0], 'order by sort');
        $this->assertSame($shippingTpl->toArray(), $ret['data'][1]);
    }

    public function testPost()
    {
        $data = [
            'name' => '测试',
            'isFreeShipping' => true,
            'valuationType' => ShippingTplModel::VALUATION_TYPE_BY_PIECE,
            'sort' => 100,
        ];

        $rules = [
            [
                'serviceId' => 1,
                'isDefault' => true,
                'startAmount' => 2,
                'startFee' => 1.23,
                'addAmount' => 3,
                'addFee' => 2.34,
            ],
            [
                'serviceId' => 1,
                'isDefault' => false,
                'regionIds' => [
                    110000,
                ],
                'startAmount' => 2,
                'startFee' => 1.23,
                'addAmount' => 3,
                'addFee' => 2.34,
            ],
        ];

        $ret = Tester::postAdminApi('shipping-tpls', $data + ['rules' => $rules]);
        $this->assertRetSuc($ret);

        /** @var ShippingTplModel $shippingTpl */
        $shippingTpl = $ret['data'];

        $this->assertArrayContains($data, $shippingTpl->toArray());
        $this->assertArrayContains($rules[0], $shippingTpl->rules[0]->toArray());
        $this->assertArrayContains($rules[1], $shippingTpl->rules[1]->toArray());
    }

    public function testPostWithInvalidValuationType()
    {
        $ret = Tester::postAdminApi('shipping-tpls', [
            'name' => '测试',
            'valuationType' => 'a',
        ]);

        $this->assertRetErr($ret, '计价方式必须在指定的数据中');
    }

    public function testPostWithoutRules()
    {
        $ret = Tester::postAdminApi('shipping-tpls', [
            'name' => '测试',
        ]);
        $this->assertRetErr($ret, '运费规则不能为空');
    }

    public function testPostFreeShippingWithoutRules()
    {
        $ret = Tester::postAdminApi('shipping-tpls', [
            'name' => '测试',
            'isFreeShipping' => true,
        ]);
        $this->assertRetSuc($ret);
    }

    public function testPostDefaultRuleWithoutRegionIds()
    {
        $ret = Tester::postAdminApi('shipping-tpls', [
            'name' => '测试',
            'rules' => [
                [
                    'serviceId' => 1,
                    'isDefault' => true,
                    'startAmount' => 1,
                    'startFee' => 1.34,
                    'addAmount' => 2,
                    'addFee' => 3,
                ],
            ],
        ]);

        $this->assertRetSuc($ret);
    }
}
