<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\LogisticsProviderTrait;
use Miaoxing\Logistics\Metadata\LogisticsTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

class LogisticsProviderModel extends BaseModel
{
    use ModelTrait;
    use LogisticsProviderTrait;
    use SoftDeleteTrait;
    use ReqQueryTrait;
}
