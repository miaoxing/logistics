<?php

namespace MiaoxingTest\Logistics\Pages\AdminApi\LogisticsAddresses;

use Miaoxing\Logistics\Service\LogisticsAddressModel;
use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Test\BaseTestCase;

class IdTest extends BaseTestCase
{
    public function testPatch()
    {
        $address = LogisticsAddressModel::save([
            'name' => '测试地址',
            'phone' => '1380138000',
            'regionId' => 140222,
            'address' => '详细地址',
            'sort' => 100,
        ]);

        $ret = Tester::patchAdminApi('logistics-addresses/' . $address->id, [
            'name' => '新测试',
        ]);

        $this->assertRetSuc($ret);
        $this->assertSame($address->id, $ret['data']->id);
    }
}
