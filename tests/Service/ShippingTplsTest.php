<?php

namespace MiaoxingTest\Logistics\Service;

class ShippingTplsTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    public function tearDown()
    {
        parent::tearDown();

        // 清空运费模板和规则
        $tpls = wei()->shippingTpl()->curApp()->findAll();
        foreach ($tpls as $tpl) {
            $tpl->getRules()->destroy();
        }
        $tpls->destroy();
    }

    protected function getFreeShippingTpl()
    {
        return wei()->shippingTpl()->fromArray([
            'freeShipping' => '1',
        ]);
    }

    protected function getDefaultShippingTpl()
    {
        $tpl = wei()->shippingTpl()->setAppId()->save([
            'name' => '测试',
            'logisticsIds' => [
                '1', '2',
            ],
            'useLogisticsIds' => [
                '1' => '0',
                '2' => '0',
            ],
        ]);

        $rules = wei()->shippingTplRule()->beColl();
        $tpl->setRules($rules);

        // 默认快递
        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '1',
            'isDefault' => '1',
            'startFee' => '11.00',
            'plusFee' => '3.00',
        ]);

        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '1',
            'isDefault' => '0',
            'areas' => [
                '440300', '440100', // 深圳市,广州市
            ],
            'startFee' => '8.00',
            'plusFee' => '1.00',
        ]);

        // 物流服务2
        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '2',
            'isDefault' => '1',
            'startFee' => '10.00',
            'plusFee' => '2.00',
        ]);

        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '2',
            'isDefault' => '0',
            'areas' => [
                '440100', // 广州市
            ],
            'startFee' => '9.00',
            'plusFee' => '1.00',
        ]);

        $rules->save();

        return $tpl;
    }

    protected function getUseShippingTpl()
    {
        $tpl = wei()->shippingTpl()->setAppId()->save([
            'name' => '测试',
            'logisticsIds' => [
                '1', '2', '3',
            ],
            'useLogisticsIds' => [
                '1' => '0',
                '2' => '0',
                '3' => '2', // 使用物流2的配置
            ],
        ]);

        $rules = wei()->shippingTplRule()->beColl();
        $tpl->setRules($rules);

        // 默认快递
        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '1',
            'isDefault' => '1',
            'startFee' => '10.00',
            'plusFee' => '3.00',
        ]);

        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '1',
            'isDefault' => '0',
            'areas' => [
                '440300', '440100', // 深圳市,广州市
            ],
            'startFee' => '8.00',
            'plusFee' => '1.00',
        ]);

        // 物流2
        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '2',
            'isDefault' => '1',
            'startFee' => '10.02',
            'plusFee' => '2.02',
        ]);

        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '2',
            'isDefault' => '0',
            'areas' => [
                '440100', '310100', // 广州市,上海市市辖区
            ],
            'startFee' => '9.02',
            'plusFee' => '1.02',
        ]);

        // 物流3 有默认规则,但是初始化时会被清空
        $rules[] = wei()->shippingTplRule()->fromArray([
            'shippingTplId' => $tpl['id'],
            'logisticsId' => '3',
            'isDefault' => '1',
            'startFee' => '10.03',
            'plusFee' => '2.03',
        ]);

        $rules->save();

        return $tpl;
    }

    public function testGetRulesByCity()
    {
        $tpl = $this->getDefaultShippingTpl();

        $rules = $tpl->getRulesByCity('深圳市');

        $this->assertEquals(2, $rules->length());

        // 匹配第一条
        $this->assertEquals('8.00', $rules[1]['startFee']);

        // 匹配第二条
        $this->assertEquals('10.00', $rules[2]['startFee']);
    }

    public function testGetRulesBySpecialCity()
    {
        $tpl = $this->getUseShippingTpl();

        $rules = $tpl->getRulesByCity('上海市');

        $this->assertEquals(3, $rules->length());

        // 匹配第一条
        $this->assertEquals('10.00', $rules[1]['startFee']);

        // 匹配第二条
        $this->assertEquals('9.02', $rules[2]['startFee']);

        // 匹配第三条
        $this->assertEquals('9.02', $rules[3]['startFee']);
    }

    public function testGetRulesBySpecialCityId()
    {
        $tpl = $this->getUseShippingTpl();

        $rules = $tpl->getRulesByCity(310100);

        $this->assertEquals(3, $rules->length());

        // 匹配第一条
        $this->assertEquals('10.00', $rules[1]['startFee']);

        // 匹配第二条
        $this->assertEquals('9.02', $rules[2]['startFee']);

        // 匹配第三条
        $this->assertEquals('9.02', $rules[3]['startFee']);
    }

    /**
     * 测试没有城市时,返回默认规则
     */
    public function testGetRulesByCityWithEmptyCity()
    {
        $tpl = $this->getDefaultShippingTpl();

        $rules = $tpl->getRulesByCity();

        $this->assertEquals(2, $rules->length());

        $this->assertEquals('1', $rules[1]['isDefault']);

        $this->assertEquals('1', $rules[2]['isDefault']);
    }

    /**
     * 测试使用xx模板的处理情况
     */
    public function testUseLogistics()
    {
        $tpl = $this->getUseShippingTpl();

        $useLogisticsIds = $tpl['useLogisticsIds'];

        $this->assertEquals('2', $useLogisticsIds['3']);

        $rules = $tpl->getRulesByCity('上海市');

        $this->assertEquals(3, $rules->length());

        // 物流3使用物流2的规则
        $this->assertEquals('9.02', $rules[3]['startFee']);
    }

    /**
     * 测试根据购物车和地址,获取可用的运费规则
     */
    public function ignoreTestFreeGetFees()
    {
        // 包邮的运费模板
        $shippingTpl = $this->getFreeShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        $carts = wei()->cart()->beColl();

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $shippingTpl);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        $services = wei()->shippingTpl->getShippingServices($carts);

        $this->assertEquals(1, count($services));

        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => '0.00'], $services[0]);
    }

    public function ignoreTestGetFees()
    {
        // 默认运费模板
        $shippingTpl = $this->getDefaultShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        $carts = wei()->cart()->beColl();

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $shippingTpl);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $this->assertEquals(2, count($services));

        // 10首 + 2续 * (10 - 1)件 + 2续 * 5件 = 38
        $this->assertEquals(['id' => '2', 'name' => '到店自提', 'fee' => 38], $services[0]);

        // 8首 + 1续 * (10 - 1)件 + 1续 * 5件 = 22
        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => 22], $services[1]);
    }

    /**
     * 测试虚拟商品的订单运费
     */
    public function ignoreTestVirtualProductWithShippingTpl()
    {
        $shippingTpl = $this->getDefaultShippingTpl();

        $product = wei()->product();
        $skuId = wei()->seq();
        $attrId = wei()->seq();
        $product->create([
            'id' => wei()->seq(),
            'name' => '测试商品',
            'price' => '100',
            'quantity' => 10,
            'virtual' => 1,
            'images' => [
                'test.jpg',
            ],
            'skus' => [
                [
                    'id' => $skuId,
                    'price' => '100.00',
                    'quantity' => 23,
                    'attrIds' => [$attrId],
                ],
            ],
            'skuConfigs' => [
                [
                    'id' => wei()->seq(),
                    'name' => '颜色',
                    'attrs' => [
                        [
                            'id' => $attrId,
                            'value' => '白色',
                        ],
                    ],
                ],
            ],
        ]);

        $product->setOption('shippingTpl', $shippingTpl);

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart->setProduct($product);
        $carts = wei()->cart()->beColl();
        $carts[] = $cart;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);
        $services = wei()->shippingTpl->getShippingServices($carts, $address);
        $this->assertEquals(0, $services[1]['fee']);

        // 改成需要地址的
        $product['virtual'] = 0;
        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product);
        $carts2 = wei()->cart()->beColl();
        $carts2[] = $cart2;

        $services = wei()->shippingTpl->getShippingServices($carts2, $address);
        $this->assertEquals('12.00', $services[1]['fee']);
    }

    public function ignoreTestGetFeesWithFreeShippingTpl()
    {
        // 运费模板1
        $shippingTpl = $this->getDefaultShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        $carts = wei()->cart()->beColl();

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 运费模板2
        $shippingTpl2 = $this->getFreeShippingTpl();

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $shippingTpl2);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $this->assertEquals(2, count($services));

        // 10首 + 2续 * (10 - 1)件 + 0续 * 5件 = 28
        $this->assertEquals(['id' => '2', 'name' => '到店自提', 'fee' => 28], $services[0]);

        // 8首 + 1续 * (10 - 1)件 + 0续 * 5件 = 17
        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => 17], $services[1]);
    }

    /**
     * 根据购物车和地址,获取可用的运费规则
     */
    public function ignoreTestGetFeesWithDiffTpl()
    {
        // 购物车coll
        $carts = wei()->cart()->beColl();

        // 运费模板1
        $shippingTpl = $this->getDefaultShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 运费模板2
        $useShippingTpl = $this->getUseShippingTpl();

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $useShippingTpl);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $this->assertEquals(2, count($services));

        // 10.02首 + 2.02续 * (5 - 1)件 + 2续 * 10件
        $this->assertEquals(['id' => '2', 'name' => '到店自提', 'fee' => 38.10], $services[0]);

        // 8首 + 1续 * (10 - 1)件 + 1深圳续 * 5件 = 22
        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => 22], $services[1]);
    }

    /**
     * 根据购物车和地址,获取可用的运费规则
     */
    public function ignoreTestGetFeesWithNewTpl()
    {
        // 购物车coll
        $carts = wei()->cart()->beColl();

        // 运费模板1
        $shippingTpl = $this->getDefaultShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 运费模板2
        $shippingTpl2 = wei()->shippingTpl();

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $shippingTpl2);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $this->assertEquals(2, count($services));

        // 10首 + 2续 * (10 - 1)件 + 0续 * 5件 = 28
        $this->assertEquals(['id' => '2', 'name' => '到店自提', 'fee' => 28], $services[0]);

        // 8首 + 1续 * (10 - 1)件 + 0续 * 5件 = 17
        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => 17], $services[1]);
    }

    /**
     * 根据购物车和地址,获取可用的运费规则
     */
    public function ignoreTestGetFeesWithEmptyTpl()
    {
        // 购物车coll
        $carts = wei()->cart()->beColl();

        // 运费模板1
        $shippingTpl = $this->getDefaultShippingTpl();

        // 产品1
        $product = wei()->product();
        $product->setOption('shippingTpl', $shippingTpl);

        // 购物车1
        $cart = wei()->cart()->fromArray([
            'quantity' => 10,
        ]);
        $cart->setProduct($product);
        $carts[] = $cart;

        // 运费模板2
        $shippingTpl2 = wei()->shippingTpl()->setAppId()->save();

        // 产品2
        $product2 = wei()->product();
        $product2->setOption('shippingTpl', $shippingTpl2);

        // 购物车2
        $cart2 = wei()->cart()->fromArray([
            'quantity' => 5,
        ]);
        $cart2->setProduct($product2);
        $carts[] = $cart2;

        // 地址1
        $address = wei()->address()->fromArray(['city' => '深圳市']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $this->assertEquals(2, count($services));

        // 10首 + 2续 * (10 - 1)件 + 0续 * 5件 = 28
        $this->assertEquals(['id' => '2', 'name' => '到店自提', 'fee' => 28], $services[0]);

        // 8首 + 1续 * (10 - 1)件 + 0续 * 5件 = 17
        $this->assertEquals(['id' => '1', 'name' => '快递', 'fee' => 17], $services[1]);
    }

    /**
     * 包邮的运费模板,获取运费信息时,返回包邮
     */
    public function testGetFreeShippingConfig()
    {
        $tpl = $this->getFreeShippingTpl();

        $config = $tpl->getShippingConfig();

        $this->assertNull($config['city']);

        $this->assertEquals('0.00', $config['services'][0]['fee']);
    }

    /**
     * 根据城市获取运费配置
     */
    public function testGetShippingConfig()
    {
        $tpl = $this->getDefaultShippingTpl();

        wei()->view->assign('block', wei()->block);

        $mock = $this->getServiceMock('lbs', ['getIpInfo']);

        $mock->expects($this->once())
            ->method('getIpInfo')
            ->willReturn([
                'city' => '深圳市',
            ]);

        $config = $tpl->getShippingConfig();

        $this->assertEquals('深圳市', $config['city']);

        $this->assertEquals('8.00', $config['services'][0]['fee']);

        $this->assertEquals('到店自提', $config['services'][1]['name']);
    }

    public function testAfterFind()
    {
        wei()->shippingTpl()->save();
        $shippingTpl = wei()->shippingTpl()->find();

        $this->assertInternalType('array', $shippingTpl['logisticsIds']);

        $this->assertInternalType('array', $shippingTpl['useLogisticsIds']);
    }
}
