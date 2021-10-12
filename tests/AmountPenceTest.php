<?php
declare(strict_types = 1);

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class AmountPenceTest extends TestCase
{
    public function testToString()
    {
        static::assertSame('&pound;0.00', (string)new AmountPence(0));
        static::assertSame('&pound;1.00', (string)new AmountPence(100));
        static::assertSame('&pound;12.34', (string)new AmountPence(1234));
        static::assertSame('&pound;123456789.01', (string)new AmountPence(12345678901));
    }

    public function testPlusMinus()
    {
        $initial = new AmountPence(1000);

        $higher = $initial->plus(new AmountPence(500));
        $lower = $higher->minus(new AmountPence(200));

        static::assertEquals(1500, $higher->getValue());
        static::assertEquals(1300, $lower->getValue());

        static::assertNotSame($initial, $higher);
        static::assertNotSame($higher, $lower);
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
            ['1,234.56', 123456],
        ];
    }

    /**
     * @dataProvider provideFromHumanReadableString
     * @param string $string
     * @param int $pence
     */
    public function testFromHumanReadableString(string $string, int $pence)
    {
        $price = AmountPence::fromHumanReadableString($string);
        self::assertInstanceOf(AmountPence::class, $price);
        self::assertSame($pence, $price->getValue());
    }
}
