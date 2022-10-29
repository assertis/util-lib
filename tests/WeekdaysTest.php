<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class WeekdaysTest extends TestCase
{

    /**
     * @return array
     */
    public function provideInvalidDefinitions(): array
    {
        return [
            ['Mon Tue'],
            ['wed'],
            [''],
        ];
    }

    /**
     * @dataProvider provideInvalidDefinitions
     * @param string $input
     */
    public function testShouldThrowExceptionOnInvalidFormat($input)
    {
        $this->expectException(\InvalidArgumentException::class);
        Weekdays::fromString($input);
    }

    /**
     * @return array
     */
    public function provideSingleWeekdays(): array
    {
        return [
            ['Mon', true, false, false, false, false, false, false],
            ['Tue', false, true, false, false, false, false, false],
            ['Wed', false, false, true, false, false, false, false],
            ['Thu', false, false, false, true, false, false, false],
            ['Fri', false, false, false, false, true, false, false],
            ['Sat', false, false, false, false, false, true, false],
            ['Sun', false, false, false, false, false, false, true],
            ['Mon,Tue,Wed,Thu,Fri,Sat,Sun', true, true, true, true, true, true, true],
        ];
    }

    /**
     * @dataProvider provideSingleWeekdays
     * @param string $input
     * @param bool $isMon
     * @param bool $isTue
     * @param bool $isWed
     * @param bool $isThu
     * @param bool $isFri
     * @param bool $isSat
     * @param bool $isSun
     */
    public function testShouldSetInputProperly($input, $isMon, $isTue, $isWed, $isThu, $isFri, $isSat, $isSun)
    {
        $weekdays = Weekdays::fromString($input);
        $this->assertEquals($weekdays->monday(), $isMon);
        $this->assertEquals($weekdays->tuesday(), $isTue);
        $this->assertEquals($weekdays->wednesday(), $isWed);
        $this->assertEquals($weekdays->thursday(), $isThu);
        $this->assertEquals($weekdays->friday(), $isFri);
        $this->assertEquals($weekdays->saturday(), $isSat);
        $this->assertEquals($weekdays->sunday(), $isSun);
    }

    public function testSerialization()
    {
        $input = 'Mon,Wed,Fri,Sun';
        $str = 'Mon, Wed, Fri, Sun';
        $json = '{"Mon":true,"Tue":false,"Wed":true,"Thu":false,"Fri":true,"Sat":false,"Sun":true}';
        $weekdays = Weekdays::fromString($input);
        $this->assertEquals($str, (string)$weekdays);
        $this->assertEquals($json, json_encode($weekdays));
    }

    public function testMatches()
    {
        $right = Date::fromString(date('Y-m-d', strtotime('next Monday')));
        $wrong = Date::fromString(date('Y-m-d', strtotime('next Tuesday')));

        $weekdays = Weekdays::fromString('Mon');

        $this->assertTrue($weekdays->matches($right));
        $this->assertFalse($weekdays->matches($wrong));
    }
}
