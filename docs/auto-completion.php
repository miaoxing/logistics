<?php

/**
 * @property    Miaoxing\Logistics\Service\Kuaidi100 $kuaidi100
 */
class Kuaidi100Mixin {
}

/**
 * @property    Miaoxing\Logistics\Service\Logistics $logistics 物流服务商
 */
class LogisticsMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\LogisticsProviderModel $logisticsProviderModel
 * @method      Miaoxing\Logistics\Service\LogisticsProviderModel logisticsProviderModel() 返回当前对象
 */
class LogisticsProviderModelMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\LogisticsServiceModel $logisticsServiceModel
 * @method      Miaoxing\Logistics\Service\LogisticsServiceModel logisticsServiceModel() 返回当前对象
 */
class LogisticsServiceModelMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\ShippingTpl $shippingTpl
 */
class ShippingTplMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\ShippingTplModel $shippingTplModel
 * @method      Miaoxing\Logistics\Service\ShippingTplModel shippingTplModel() 返回当前对象
 */
class ShippingTplModelMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\ShippingTplRule $shippingTplRule
 */
class ShippingTplRuleMixin {
}

/**
 * @property    Miaoxing\Logistics\Service\ShippingTplRuleModel $shippingTplRuleModel
 * @method      Miaoxing\Logistics\Service\ShippingTplRuleModel shippingTplRuleModel() 返回当前对象
 */
class ShippingTplRuleModelMixin {
}

/**
 * @mixin Kuaidi100Mixin
 * @mixin LogisticsMixin
 * @mixin LogisticsProviderModelMixin
 * @mixin LogisticsServiceModelMixin
 * @mixin ShippingTplMixin
 * @mixin ShippingTplModelMixin
 * @mixin ShippingTplRuleMixin
 * @mixin ShippingTplRuleModelMixin
 */
class AutoCompletion {
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion;
}

/** @var Miaoxing\Logistics\Service\Kuaidi100 $kuaidi100 */
$kuaidi100 = wei()->kuaidi100;

/** @var Miaoxing\Logistics\Service\Logistics $logistics */
$logistics = wei()->logistics;

/** @var Miaoxing\Logistics\Service\LogisticsProviderModel $logisticsProvider */
$logisticsProvider = wei()->logisticsProviderModel;

/** @var Miaoxing\Logistics\Service\LogisticsProviderModel|Miaoxing\Logistics\Service\LogisticsProviderModel[] $logisticsProviders */
$logisticsProviders = wei()->logisticsProviderModel();

/** @var Miaoxing\Logistics\Service\LogisticsServiceModel $logisticsService */
$logisticsService = wei()->logisticsServiceModel;

/** @var Miaoxing\Logistics\Service\LogisticsServiceModel|Miaoxing\Logistics\Service\LogisticsServiceModel[] $logisticsServices */
$logisticsServices = wei()->logisticsServiceModel();

/** @var Miaoxing\Logistics\Service\ShippingTpl $shippingTpl */
$shippingTpl = wei()->shippingTpl;

/** @var Miaoxing\Logistics\Service\ShippingTplModel $shippingTpl */
$shippingTpl = wei()->shippingTplModel;

/** @var Miaoxing\Logistics\Service\ShippingTplModel|Miaoxing\Logistics\Service\ShippingTplModel[] $shippingTpls */
$shippingTpls = wei()->shippingTplModel();

/** @var Miaoxing\Logistics\Service\ShippingTplRule $shippingTplRule */
$shippingTplRule = wei()->shippingTplRule;

/** @var Miaoxing\Logistics\Service\ShippingTplRuleModel $shippingTplRule */
$shippingTplRule = wei()->shippingTplRuleModel;

/** @var Miaoxing\Logistics\Service\ShippingTplRuleModel|Miaoxing\Logistics\Service\ShippingTplRuleModel[] $shippingTplRules */
$shippingTplRules = wei()->shippingTplRuleModel();
