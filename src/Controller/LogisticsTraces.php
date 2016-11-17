<?php

namespace Miaoxing\Logistics\Controller;

class LogisticsTraces extends \miaoxing\plugin\BaseController
{
    public function indexAction($req)
    {
        $logistics = wei()->logistics()->findOneById($req['logisticsId']);

        $ret = $logistics->getTraces($req['logisticsNo']);
        if (isset($ret['next'])) {
            return $this->response->redirect($ret['next']);
        }

        $traces = $ret['traces'];
        $headerTitle = '物流跟踪';
        return get_defined_vars();
    }
}
