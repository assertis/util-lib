<?php

declare(strict_types=1);

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class AmountMoneyTest extends TestCase
{

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
            ['0.00', 0],
            ['0,00', 0],
            ['.00', 0],
            [',00', 0],
            ['', 0],
        ];
    }

    /**
     * @dataProvider provideFromHumanReadableString
     * @param string $string
     * @param int $pence
     */
    public function testFromHumanReadableString(string $string, int $pence)
    {
        $price = AmountMoney::fromHumanReadableString($string);
        self::assertInstanceOf(AmountMoney::class, $price);
        self::assertSame($pence, $price->getValue());
    }

}
