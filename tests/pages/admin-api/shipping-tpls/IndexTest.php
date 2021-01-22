<?php

namespace MiaoxingTest\Category\Pages\AdminApi\Categories;

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
}
