<?php

namespace MiaoxingTest\Logistics\Pages\AdminApi\LogisticsAddresses;

use Miaoxing\Logistics\Service\LogisticsAddressModel;
use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;

class IndexTest extends BaseTestCase
{
    public function testGet()
    {
        User::loginById(1);

        $maxSort = LogisticsAddressModel::max('sort');

        $address = LogisticsAddressModel::save([
            'name' => '测试地址' . ($maxSort + 1),
            'sort' => $maxSort + 1,
        ]);
        $address2 = LogisticsAddressModel::save([
            'name' => '测试地址' . ($maxSort + 2),
            'sort' => $maxSort + 2,
        ]);

        $ret = Tester::request(['limit' => 2])->getAdminApi('logistics-addresses');

        $this->assertArrayContains($address2->toArray(), $ret['data'][0], 'order by sort');
        $this->assertArrayContains($address->toArray(), $ret['data'][1]);
    }

    public function testPost()
    {
        $data = [
            'name' => '测试地址',
            'phone' => '1380138000',
            'regionId' => 140222,
            'address' => '详细地址',
            'sort' => 100,
        ];

        $ret = Tester::postAdminApi('logistics-addresses', $data);
        $this->assertRetSuc($ret);

        /** @var LogisticsAddressModel $address */
        $address = $ret['data'];

        $this->assertArrayContains($data, $address->toArray());
    }

    public function testPostWithoutName()
    {
        $ret = Tester::postAdminApi('logistics-addresses', []);

        $this->assertRetErr($ret, '联系人姓名不能为空');
    }

    public function testPostWithoutPhone()
    {
        $ret = Tester::postAdminApi('logistics-addresses', [
            'name' => '测试',
        ]);
        $this->assertRetErr($ret, '联系人电话不能为空');
    }

    public function testPostWithoutRegionId()
    {
        $ret = Tester::postAdminApi('logistics-addresses', [
            'name' => '测试',
            'phone' => '1380138000',
        ]);
        $this->assertRetErr($ret, '地区不能为空');
    }

    public function testPostWithoutAddress()
    {
        $ret = Tester::postAdminApi('logistics-addresses', [
            'name' => '测试',
            'phone' => '1380138000',
            'regionId' => 140222,
        ]);
        $this->assertRetErr($ret, '详细地址不能为空');
    }
}
