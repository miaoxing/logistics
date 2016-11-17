<?php

namespace MiaoxingTest\Logistics\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * 演示服务
 */
class LogisticsRecordTest extends BaseTestCase
{
    /**
     * 获取名称
     */
    public function testGetName()
    {
        $this->assertSame('logistics', wei()->logisticsRecord->getName());
    }
}
