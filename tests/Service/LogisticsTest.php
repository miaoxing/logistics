<?php

namespace MiaoxingTest\Logistics\Service;

class LogisticsTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    public function testGetNames()
    {
        $this->assertInternalType('array', wei()->logistics->getNames());
    }

    public function testGetName()
    {
        $this->assertInternalType('string', wei()->logistics->getName(1));

        $this->assertInternalType('string', wei()->logistics->getName(-1));
    }
}
