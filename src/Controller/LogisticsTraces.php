<?php

namespace Miaoxing\Logistics\Controller;

/**
 * @mixin \PageMixin
 * @mixin \ResponseMixin
 */
class LogisticsTraces extends \Miaoxing\Plugin\BaseController
{
    public function indexAction($req)
    {
        $logistics = wei()->logistics()->findOneById($req['logisticsId']);

        $ret = $logistics->getTraces($req['logisticsNo']);

        // TODO 判断小程序中就不打开网页
        if (isset($ret['next']) && !wei()->plugin->isInstalled('wxa')) {
            return $this->response->redirect($ret['next']);
        }

        $traces = isset($ret['traces']) ? $ret['traces'] : [];
        $this->page->setTitle('物流跟踪');

        return get_defined_vars();
    }
}
