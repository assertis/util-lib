<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class AmountPenceTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $this->assertSame('&pound;0.00', (string)new AmountPence(0));
        $this->assertSame('&pound;1.00', (string)new AmountPence(100));
        $this->assertSame('&pound;12.34', (string)new AmountPence(1234));
        $this->assertSame('&pound;123456789.01', (string)new AmountPence(12345678901));
    }
}
