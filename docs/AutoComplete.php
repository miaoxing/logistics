<?php

namespace plugins\logistics\docs {

    use Miaoxing\Logistics\Service\Kuaidi100;
    use Miaoxing\Logistics\Service\Logistics;
    use Miaoxing\Logistics\Service\ShippingTpl;
    use Miaoxing\Logistics\Service\ShippingTplRule;

    /**
     * @property    Logistics $logistics 物流服务商
     * @method      Logistics|Logistics[] logistics()
     *
     * @property    ShippingTpl $shippingTpl 运费模板
     * @method      ShippingTpl|ShippingTpl[] shippingTpl()
     * @method      ShippingTplRule|ShippingTplRule[] shippingTplRule() 运费模板规则
     *
     * @property    Kuaidi100 $kuaidi100
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return \plugins\logistics\docs\AutoComplete
     */
    function wei()
    {
    }
}
