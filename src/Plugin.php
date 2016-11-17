<?php

namespace Miaoxing\Logistics;

use Miaoxing\Address\Service\Address;
use plugins\mall\services\Order;
use plugins\product\services\Product;
use Wei\RetTrait;

class Plugin extends \miaoxing\plugin\BasePlugin
{
    use RetTrait;

    protected $name = '物流管理,包括运费模板等';

    protected $adminNavId = 'products';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $navs[] = [
            'parentId' => 'products-service',
            'url' => 'admin/shipping-tpls',
            'name' => '运费模板管理',
            'sort' => 20,
        ];

        $navs[] = [
            'parentId' => 'orders-service',
            'url' => 'admin/logistics-traces',
            'name' => '物流跟踪',
        ];
    }

    /**
     * 商品详情页,展示配送方式,运费等
     *
     * @param Product $product
     */
    public function onProductsShowItem(Product $product)
    {
        if ($product['virtual']) {
            return;
        }

        $shippingTpl = $product->getShippingTpl();

        $config = $shippingTpl->getShippingConfig();

        $city = $config['city'];
        $services = $config['services'];
        $defaultServices = isset($services[1]) ? $services[1] : $services[0];

        $this->view->display('logistics:shippingTpls/productsShowItem.php', get_defined_vars());
    }

    public function onPostOrderCartRender(Order $order, Address $address = null)
    {
        $this->view->display('logistics:shippingTpls/postOrderCartRender.php', [
            'addressId' => $address ? $address['id'] : 0,
        ]);
    }

    /**
     * 根据指定的物流服务,计算运费
     */
    public function onPreOrderCreate(Order $order, Address $address = null, $data, array $options = [])
    {
        if (isset($options['requireAddress']) && !$options['requireAddress']) {
            return;
        }
        if (!$order->isRequireAddress()) {
            return;
        }

        if (!$address) {
            return $this->err('请选择收货地址', -9);
        }

        if (!$data['userLogisticsId']) {
            return $this->err('请选择配送方式', -10);
        }

        $order['userLogisticsId'] = $data['userLogisticsId'];
        $carts = $order->getCarts();
        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        $useService = false;
        foreach ($services as $service) {
            if ($service['id'] == $data['userLogisticsId']) {
                $useService = $service;
                break;
            }
        }
        if (!$useService) {
            return $this->err('配送方式不存在,请重新选择', -8);
        }

        $order->setAmountRule('address', ['name' => '运费', 'shippingFee' => $useService['fee']]);
    }

    /**
     * 订单导出事件
     *
     * @param array $order
     * @param array $cart
     * @param array $rowData
     * @param array $outputData
     */
    public function onRenderOrder(array $order, array $cart, array &$rowData, array &$outputData)
    {
        if (!in_array('物流', $outputData[0])) {
            $outputData[0][] = '物流';
        }

        if (!in_array('用户指定物流', $outputData[0])) {
            $outputData[0][] = '用户指定物流';
        }

        if (!in_array('运单号', $outputData[0])) {
            $outputData[0][] = '运单号';
        }

        $rowData[] = $order['logisticsName'];
        $rowData[] = $order['userLogisticsName'];
        $rowData[] = '\'' . $order['logisticsNo'];
    }
}
