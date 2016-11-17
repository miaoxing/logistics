<?php

namespace MiaoxingTest\Logistics\Controller;

class LogisticsTracesTest extends \Miaoxing\Plugin\Test\BaseControllerTestCase
{
    public function testGetJson()
    {
        $tester = wei()->tester()
            ->login()
            ->controller('admin/shippingTpls')
            ->json()
            ->exec();

        $res = $tester->response();

        $this->assertEquals(1, $res['code']);
    }
}
