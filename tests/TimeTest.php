<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TimeTest extends TestCase
{

    public function testFromStringWithEmptyStringShouldReturnNull()
    {
        $this->assertEquals(null, Time::fromString('    '));
    }

    public function testFromStringWithWrongFormatShouldThrowException()
    {
        $this->expectException(\UnexpectedValueException::class);
        Time::fromString('34:67');
    }

    /**
     * @return array
     */
    public function provideShouldAcceptFormats(): array
    {
        return [
            ['0123', '1:23'],
            ['1:23', '1:23'],
            ['12:34:56', '12:34'],
        ];
    }

    /**
     * @dataProvider provideShouldAcceptFormats
     * @param string $input
     * @param string $expected
     */
    public function testShouldAcceptFormats(string $input, string $expected)
    {
        $this->assertSame($expected, (string)Time::fromString($input));
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
