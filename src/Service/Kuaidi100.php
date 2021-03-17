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
            'userAgent' => implode(' ', [
                'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N)',
                'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Mobile Safari/537.36'
            ]),
            'referer' => true,
            'dataType' => 'json',
            'throwException' => false,
        ]);

        if (!isset($http['message']) || 'ok' !== $http['message']) {
            $message = isset($http['message']) ? $http['message'] : '请求失败';
            $ret = $http->toRet($this->err($message));
            $this->logger->warning('快递查询失败', $ret);
            return $ret;
        }

        $http['traces'] = $http['data'];
        unset($http['data']);
        return $http->toRet($this->suc());
    }
}
