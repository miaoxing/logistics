<?php

namespace MiaoxingTest\Logistics\Controller\Admin;

class ShippingTplsTest extends \Miaoxing\Plugin\Test\BaseControllerTestCase
{
    public function testJson()
    {
        wei()->request->set('_format', 'json');

        $response = $this->dispatch('admin/shippingTpls', 'index');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, $json['code']);
    }

    public function testDestroyButShippingTplIsUsed()
    {
        // 测试模板被使用
        $shippingTpl = wei()->shippingTpl()->setAppId()->save();

        $product = wei()->product()->save([
            'name' => '测试商品',
            'shippingTplId' => $shippingTpl['id'],
        ]);

        wei()->request->set('id', $shippingTpl['id']);
        $response = $this->dispatch('admin/shippingTpls', 'destroy');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals('很抱歉,该模板已被商品"测试商品"使用,不能删除', $json['message']);

        // 测试
        $product->destroy();

        wei()->request->set('id', $shippingTpl['id']);
        $response = $this->dispatch('admin/shippingTpls', 'destroy');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, $json['code']);

        $shippingTpl->reload();

        $this->assertNotEquals('0000-00-00 00:00:00', $shippingTpl['deleteTime']);
    }

    public function testCreate()
    {
        wei()->request->set([
            'name' => '模板名称',
            'freeShipping' => '0',
            'logisticsIds' => [
                '1',
            ],
            'useLogisticsIds' => [
                '1' => 0,
            ],
            'rules' => [
                [
                    'logisticsId' => '1',
                    'isDefault' => '1',
                    'areas' => [
                        '深圳市',
                    ],
                    'areaNames' => [
                        '深圳市',
                    ],
                    'startFee' => '1.00',
                    'plusFee' => '1.00',
                ],
            ],
        ]);
        $response = $this->dispatch('admin/shippingTpls', 'create');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, $json['code']);
    }
}
