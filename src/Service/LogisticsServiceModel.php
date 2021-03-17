<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\Logistics\Metadata\LogisticsProviderTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

class LogisticsServiceModel extends BaseModel
{
    use LogisticsProviderTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use SoftDeleteTrait;
}
