<?php

namespace Miaoxing\Logistics\Service;

use Miaoxing\App\Service\Logger;
use Miaoxing\Plugin\BaseService;
use Wei\RetTrait;

/**
 * @property Logger $logger
 */
class Kuaidi100 extends BaseService
{
    use RetTrait;

    public function getTraces($name, $code)
    {
        $http = wei()->http([
            'url' => sprintf('https://www.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=', $name, $code),
            'dataType' => 'json',
            'throwException' => false,
        ]);

        if (!isset($http['message']) || $http['message'] !== 'ok') {
            $message = isset($http['message']) ?: '请求失败';
            $ret = $http->toRet($this->err($message));
            $this->logger->warning('快递查询失败', $ret);
            return $ret;
        }

        $http['traces'] = $http['data'];
        unset($http['data']);
        return $http->toRet($this->suc());
    }
}
