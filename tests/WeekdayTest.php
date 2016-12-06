<?php
declare(strict_types = 1);

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class WeekdayTest extends PHPUnit_Framework_TestCase
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
