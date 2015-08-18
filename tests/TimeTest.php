<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TimeTest extends PHPUnit_Framework_TestCase
{

    public function testFromStringWithEmptyStringShouldReturnNull()
    {
        $this->assertEquals(null, Time::fromString('    '));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testFromStringWithWrongFormatShouldThrowException()
    {
        Time::fromString('34:67');
    }

    public function testFromStringAndGetters()
    {
        $string = '0930';
        $obj = Time::fromString($string);
        $this->assertEquals(9, $obj->getHours());
        $this->assertEquals(30, $obj->getMinutes());
        $this->assertEquals('9:30', $obj->getTime());
    }

    public function testSerialization()
    {
        $string = '0930';
        $obj = Time::fromString($string);
        $this->assertEquals($obj->getTime(), (string)$obj);
        $this->assertEquals('"' . $obj->getTime() . '"', json_encode($obj));
    }

    /**
     * @return array
     */
    public function provideIsAfter()
    {
        return [
            ['0915', '0930', false],
            ['0930', '0915', true],
            ['0915', '0915', false],
            ['2359', '0000', true],
            ['0905', '0915', false],
            ['0915', '0905', true],
        ];
    }

    /**
     * @dataProvider provideIsAfter
     * @param string $firstTime
     * @param string $secondTime
     * @param bool $isAfter
     */
    public function testIsAfter($firstTime, $secondTime, $isAfter)
    {
        $first = Time::fromString($firstTime);
        $second = Time::fromString($secondTime);
        $this->assertEquals($isAfter, $first->isAfter($second));
    }
}
