<?php

namespace MiaoxingTest\Logistics\Pages\Api\Admin\ShippingTpls;

use Miaoxing\Logistics\Service\ShippingTplModel;
use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Test\BaseTestCase;

class IdTest extends BaseTestCase
{
    public function testPatch()
    {
        $shippingTpl = ShippingTplModel::save([
            'name' => '测试',
            'isFreeShipping' => true,
        ]);

        $ret = Tester::patchAdminApi('shipping-tpls/' . $shippingTpl->id, [
            'name' => '新测试',
        ]);

        $this->assertRetSuc($ret);
    }
}
