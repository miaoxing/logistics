<?php

namespace Miaoxing\Logistics\Controller;

use plugins\cart\services\Cart;

class ShippingTpls extends \miaoxing\plugin\BaseController
{
    public function getServicesAction($req)
    {
        /** @var Cart $carts */
        $carts = wei()->cart()->mine()->notOrdered()->findAll(['id' => $req['cartId']]);

        $address = wei()->address()->mine()->findOneById($req['addressId']);

        $services = wei()->shippingTpl->getShippingServices($carts, $address);

        return $this->suc([
            'services' => $services,
        ]);
    }
}
