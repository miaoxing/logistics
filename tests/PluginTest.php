<?php

namespace MiaoxingTest\Logistics;

class PluginTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped('todo upgrade');
    }

    public function ignoreTestOnProductsShowItem()
    {
        $this->expectOutputRegex('/快递 &yen;0.00/');

        wei()->view->assign('block', wei()->block);

        $product = wei()->product();
        wei()->event->trigger('productsShowItem', [$product]);
    }

    public function ignoreTestOnPostOrderCartRender()
    {
        $this->expectOutputRegex('/配送方式/');

        wei()->view->assign('block', wei()->block);

        $order = wei()->order();
        $address = wei()->address();
        wei()->event->trigger('postOrderCartRender', [$order, $address]);
    }

    public function ignoreTestOnPreOrderCreate()
    {
        wei()->curUser->loginById(1);

        $product = wei()->product();
        $ret = $product->create([
            'name' => '下单物流测试',
            'quantity' => 10,
            'price' => '10',
        ]);
        $this->assertRetSuc($ret);

        $cart = wei()->cart();
        $ret = $cart->create([
            'skuId' => $product->getSkus()[0]['id'],
            'quantity' => 1,
        ]);
        $this->assertRetSuc($ret);

        $carts = wei()->cart()->multiple();
        $carts[] = $cart;

        $order = wei()->order();
        $order->setCarts($carts);

        $address = wei()->address();
        $data = [];

        // 没有选择运费模板的情况
        $ret = wei()->event->until('preOrderCreate', [$order, $address, $data]);
        $this->assertRetErr($ret, '请选择配送方式', -10);

        // 选择运费服务为1
        $data = ['userLogisticsId' => 1];
        $ret = wei()->event->until('preOrderCreate', [$order, $address, $data]);

        $this->assertNull($ret);
        $this->assertEquals(['address' => ['name' => '运费', 'shippingFee' => '0.00']], $order->getAmountRules());

        // 选择不存在的运费服务
        $data = ['userLogisticsId' => 2];
        $ret = wei()->event->until('preOrderCreate', [$order, $address, $data]);

        $this->assertRetErr($ret, '配送方式不存在,请重新选择', -8);
    }

    public function testOnRenderOrder()
    {
        $order = [];
        $cart = [];
        $rowData = [[]];
        $outputData = [[]];

        wei()->event->trigger('renderOrder', [$order, $cart, &$rowData, &$outputData]);

        $this->assertContains('物流', $outputData[0]);
        $this->assertContains('用户指定物流', $outputData[0]);
        $this->assertContains('运单号', $outputData[0]);
    }
}
