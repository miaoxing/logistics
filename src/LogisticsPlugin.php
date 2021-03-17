<?php

namespace Miaoxing\Logistics;

use Miaoxing\Address\Service\Address;
use Miaoxing\Logistics\Service\Logistics;
use Miaoxing\Order\Service\Order;
use Miaoxing\Product\Service\Product;
use Wei\RetTrait;

class LogisticsPlugin extends \Miaoxing\Plugin\BasePlugin
{
    use RetTrait;

    protected $name = '物流管理,包括运费模板等';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $subCategories[] = [
            'parentId' => 'setting',
            'url' => 'admin/shipping-tpls',
            'name' => '运费模板管理',
            'sort' => 20,
        ];

        $subCategories[] = [
            'parentId' => 'setting',
            'url' => 'admin/logistics-addresses',
            'name' => '地址管理',
            'sort' => 10,
        ];
    }

    /**
     * 商品详情页,展示配送方式,运费等
     *
     * @param \Miaoxing\Product\Service\Product $product
     */
    public function onProductsShowItemMin(Product $product)
    {
        if ($product['isVirtual'] || $product['config']['selfPickUp']) {
            return;
        }
        if (!$product['config']['requireAddress']) {
            return;
        }

        $shippingTpl = $product->getShippingTpl();

        $config = $shippingTpl->getShippingConfig();

        $city = $config['city'];
        $services = $config['services'];
        $defaultServices = isset($services[1]) ? $services[1] : $services[0];

        $this->view->display('@logistics/shippingTpls/productsShowItem.php', get_defined_vars());
    }

    public function onPostOrderCartRender(Order $order, Address $address = null)
    {
        if (!$order->isRequireAddress()) {
            return;
        }

        $this->view->display('@logistics/shippingTpls/postOrderCartRender.php', [
            'addressId' => $address ? $address['id'] : 0,
        ]);
    }

    /**
     * 根据指定的物流服务,计算运费
     * @param mixed $data
     */
    public function onPreOrderCreate(Order $order, Address $address = null, $data, array $options = [])
    {
        // 自提自动设置
        if ($order->getCarts()->isSelfPickUp()) {
            $order['userLogisticsId'] = Logistics::ID_SELF_PICKUP;
            return;
        }

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
        if (!in_array('物流', $outputData[0], true)) {
            $outputData[0][] = '物流';
        }

        if (!in_array('用户指定物流', $outputData[0], true)) {
            $outputData[0][] = '用户指定物流';
        }

        if (!in_array('运单号', $outputData[0], true)) {
            $outputData[0][] = '运单号';
        }

        $rowData[] = $order['logisticsName'];
        $rowData[] = $order['userLogisticsName'];
        $rowData[] = '\'' . $order['logisticsNo'];
    }
}
