<?php

namespace Tests\Helpers;

use App\Helpers\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{

    public function testFormat()
    {
        $this->assertEquals('0.10', Money::format(10));
        $this->assertEquals('1,254.78', Money::format(125478));
    }

    public function testToRealMoney()
    {
        $this->assertEquals(0.1, Money::toRealMoney(10));
        $this->assertEquals(3.14, Money::toRealMoney(314));
    }

    public function testToInternalMoney()
    {
        $this->assertEquals(12, Money::toInternalMoney(0.12));
        $this->assertEquals(154, Money::toInternalMoney(1.545));
        $this->assertEquals(0, Money::toInternalMoney(0));
    }
}
