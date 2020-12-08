<?php

namespace Miaoxing\Logistics\Service;

class ShippingTplRule extends \Miaoxing\Plugin\BaseService
{
    protected $table = 'shippingTplRules';

    protected $data = [
        'areas' => [],
        'areaNames' => [],
    ];

    protected $providers = [
        'db' => 'app.db',
    ];

    public function afterFind()
    {
        parent::afterFind();
        $this['areas'] = explode(',', $this['areas']);
        $this['areaNames'] = explode(',', $this['areaNames']);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['areas'] = implode(',', (array) $this['areas']);
        $this['areaNames'] = implode(',', (array) $this['areaNames']);
    }

    public function afterSave()
    {
        parent::beforeSave();
        $this['areas'] = explode(',', $this['areas']);
        $this['areaNames'] = explode(',', $this['areaNames']);
    }
}
