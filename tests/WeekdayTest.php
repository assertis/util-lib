<?php
declare(strict_types = 1);

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class WeekdayTest extends TestCase
{
    /**
     * @return array
     */
    public function provideFromString(): array
    {
        return [
            ['Mon', 0],
            ['Monday', 0],
            ['Tue', 1],
            ['Tuesday', 1],
            ['Wed', 2],
            ['Wednesday', 2],
            ['Thu', 3],
            ['Thursday', 3],
            ['Fri', 4],
            ['Friday', 4],
            ['Sat', 5],
            ['Saturday', 5],
            ['Sun', 6],
            ['Sunday', 6],
        ];
    }

    /**
     * @dataProvider provideFromString
     * @param string $input
     * @param int $expected
     */
    public function testFromString(string $input, int $expected)
    {
        static::assertSame($expected, Weekday::fromString($input)->getDayId());
    }

    public function testFromStringWithCustomTable()
    {
        $table = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

        static::assertSame('Thursday', Weekday::fromString('TH', $table)->getLongName());
    }

    public function testFromStringWithCustomTableExcludesRegularTables()
    {
        $table = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

        $this->expectException(InvalidArgumentException::class);
        Weekday::fromString('Thursday', $table);
    }

    public function testFromStringError()
    {
        $this->expectException(InvalidArgumentException::class);
        Weekday::fromString('not a day');
    }

    public function testToString()
    {
        $day = Weekday::fromString('Mon');

        static::assertSame('Monday', $day->getLongName());
        static::assertSame('Mon', $day->getShortName());
        static::assertSame('Monday', (string)$day);
        static::assertSame(0, $day->getDayId());
    }
}
