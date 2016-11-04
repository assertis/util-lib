<?php
declare(strict_types = 1);

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class AmountPenceTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        static::assertSame('&pound;0.00', (string)new AmountPence(0));
        static::assertSame('&pound;1.00', (string)new AmountPence(100));
        static::assertSame('&pound;12.34', (string)new AmountPence(1234));
        static::assertSame('&pound;123456789.01', (string)new AmountPence(12345678901));
    }

    public function testPlus()
    {
        $initial = new AmountPence(1000);
        $new = $initial->plus(new AmountPence(500));

        static::assertEquals(1500, $new->getValue());
    }

    /**
     * @return array
     */
    public function provideFromHumanReadableString(): array
    {
        return [
            ['123.456', 12345],
            ['123.45', 12345],
            ['123.4', 12340],
            ['123', 12300],
            ['123,45', 12345],
        ];
    }

    /**
     * @dataProvider provideFromHumanReadableString
     * @param string $string
     * @param int $pence
     */
    public function testFromHumanReadableString(string $string, int $pence)
    {
        self::assertSame($pence, AmountPence::fromHumanReadableString($string)->getValue());
    }
}
