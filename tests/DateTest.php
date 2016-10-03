<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class DateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowExceptionOnInvalidFormat()
    {
        Date::fromString('invalid');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowExceptionOnInvalidValues()
    {
        Date::fromString('2014-34-56');
    }

    /**
     * @return array
     */
    public function provideFromString(): array
    {
        return [
            ['2016-05-20 12:34:56', '2016-05-20'],
            ['2016-05-20', '2016-05-20'],
            ['160520', '2016-05-20'],
        ];
    }

    /**
     * @dataProvider provideFromString
     * @param string $string
     * @param string $expected
     */
    public function testFromString(string $string, string $expected)
    {
        $this->assertSame($expected, Date::fromString($string)->formatShort());
    }

    /**
     * @return array
     */
    public function provideIsSaturday()
    {
        return [
            ['2014-12-02', false],
            ['2014-12-06', true],
        ];
    }

    /**
     * @dataProvider provideIsSaturday
     * @param string $dateStr
     * @param bool $isSaturday
     */
    public function testIsSaturday($dateStr, $isSaturday)
    {
        $date = Date::fromString($dateStr);
        $this->assertSame($isSaturday, $date->isSaturday());
    }

    /**
     * @return array
     */
    public function provideIsSunday()
    {
        return [
            ['2015-05-03', true],
            ['2015-05-13', false],
        ];
    }

    /**
     * @dataProvider provideIsSunday
     * @param string $dateStr
     * @param bool $isSunday
     */
    public function testIsSunday($dateStr, $isSunday)
    {
        $date = Date::fromString($dateStr);
        $this->assertSame($isSunday, $date->isSunday());
    }

    /**
     * @return array
     */
    public function provideIsWorkingDay()
    {
        return [
            ['2015-04-07', true],
            ['2015-04-11', false],
            ['2015-04-12', false],
            ['2015-04-13', true],
        ];
    }

    /**
     * @dataProvider provideIsWorkingDay
     * @param string $dateStr
     * @param bool $isWorkingDay
     */
    public function testIsWorkingDay($dateStr, $isWorkingDay)
    {
        $date = Date::fromString($dateStr);
        $this->assertSame($isWorkingDay, $date->isWorkingDay());
    }

    public function testDateShouldBeAtMidnight()
    {
        $date = Date::fromString('2014-01-01');
        $expected = '00:00';
        $this->assertEquals($expected, $date->format('H:i'));
    }

    public function testDateShouldSerializeToInputFormat()
    {
        $expected = '2014-01-01';
        $date = Date::fromString($expected);
        $this->assertEquals($expected, (string)$date);
    }

    public function testDateShouldJsonEncodeToInputFormat()
    {
        $input = '2014-01-01';
        $expected = "\"{$input}\"";
        $date = Date::fromString($input);
        $this->assertEquals($expected, json_encode($date));
    }

    /**
     * @return array
     */
    public function provideGetDayEarlier()
    {
        return [
            ['2014-01-01', '2013-12-31'],
            ['2014-03-01', '2014-02-28'],
            ['2016-02-29', '2016-02-28'],
        ];
    }

    /**
     * @dataProvider provideGetDayEarlier
     * @param string $today
     * @param string $dayEarlier
     */
    public function testGetDayEarlierOrLater($today, $dayEarlier)
    {
        $this->assertEquals($dayEarlier, Date::fromString($today)->getDayEarlier()->format(Date::SHORT_FORMAT));
        $this->assertEquals($today, Date::fromString($dayEarlier)->getDayLater()->format(Date::SHORT_FORMAT));
    }

    /**
     * @return array
     */
    public function provideGetMonthsLater()
    {
        return [
            ['2014-12-12', 1, '2015-01-12'],
            ['2014-01-31', 1, '2014-03-03'],

            ['2015-01-31', 1, '2015-03-03'],
            ['2015-01-31', 2, '2015-03-31'],
            ['2015-01-31', 3, '2015-05-01'],

            ['2016-01-31', 1, '2016-03-02'], // Feb 2016 has 29 days
            ['2016-01-31', 2, '2016-03-31'], // but that doesn't explain
            ['2016-01-31', 3, '2016-05-01'], // what happens here
        ];
    }

    /**
     * @dataProvider provideGetMonthsLater
     * @param string $start
     * @param int $months
     * @param string $end
     */
    public function testGetMonthsLater($start, $months, $end)
    {
        $date = Date::fromString($start);
        $later = $date->getMonthsLater($months);

        $this->assertSame($end, $later->formatShort());
    }

    /**
     * @return array
     */
    public function provideIsBefore()
    {
        return [
            ['2014-10-20', '2014-10-21', true, false],
            ['2014-10-20', '2014-10-20', false, false],
            ['2014-10-21', '2014-10-20', false, true],
        ];
    }

    /**
     * @dataProvider provideIsBefore
     * @param string $first
     * @param string $second
     * @param bool $isBefore
     * @param bool $isAfter
     */
    public function testIsBefore($first, $second, $isBefore, $isAfter)
    {
        $date1 = Date::fromString($first);
        $date2 = Date::fromString($second);
        $this->assertSame($isBefore, $date1->isBefore($date2));
        $this->assertSame($isAfter, $date1->isAfter($date2));
    }

    public function testIsSameDate()
    {
        $date1 = new Date('2015-01-03 15:00:00');
        $date2 = new Date('2015-01-03 16:00:00');
        $this->assertTrue($date1->isSameDay($date2));

        $date2 = new Date('2015-01-04 16:00:00');

        $this->assertFalse($date1->isSameDay($date2));
    }

    public function testIsSameTime()
    {
        $date1 = new Date('2015-01-03 15:15:01');

        $date2 = new Date('2016-02-04 15:15:01');
        $this->assertTrue($date1->isSameTime($date2));

        $date3 = new Date('2015-01-03 15:15:01');
        $this->assertTrue($date1->isSameTime($date3));

        $date4 = new Date('2015-01-03 15:15:02');
        $this->assertFalse($date1->isSameTime($date4));
    }

    /**
     * @return array
     */
    public function provideGetWorkingDaysEarlier()
    {
        return [
            ['2015-05-08', 5, '2015-05-03'],
            ['2015-05-27', 5, '2015-05-20'],
        ];
    }

    /**
     * @dataProvider provideGetWorkingDaysEarlier
     * @param string $startDate
     * @param int $days
     * @param string $endDate
     */
    public function testGetWorkingDaysEarlier($startDate, $days, $endDate)
    {
        $start = Date::fromString($startDate);
        $end = Date::fromString($endDate);

        $earlier = $start->getWorkingDaysEarlier($days);
        $later = $end->getWorkingDaysLater($days);

        $this->assertSame($endDate, $earlier->formatShort());
        $this->assertSame($startDate, $later->formatShort());
    }

    public function testFormatEnglish()
    {
        $this->assertStringEndsWith(date('Y'), (new Date())->formatEnglish());
    }

    /**
     * @return array
     */
    public function provideGetTwoBitYear()
    {
        return [
            ['1981-05-07', 0],
            ['2002-05-07', 1],
        ];
    }

    /**
     * @dataProvider provideGetTwoBitYear
     * @param string $date
     * @param int $bit
     */
    public function testGetTwoBitYear($date, $bit)
    {
        $obj = new Date($date);
        $this->assertSame($obj->getTwoBitYear(), $bit);
    }

    /**
     * @return array
     */
    public function provideGetNineBitDate()
    {
        return [
            ['1980-01-01 00:00:00', 000],
            ['2009-01-01 00:00:00', 353],
            ['2010-01-01 00:00:00', 206],
        ];
    }

    /**
     * @dataProvider provideGetNineBitDate
     * @param string $date
     * @param int $bits
     */
    public function testGetNineBitDate1($date, $bits)
    {
        $obj = new Date($date);
        $this->assertSame($bits, $obj->getNineBitDate());
    }

    /**
     * @return array
     */
    public function provideGetNineBitMinuteOfDay()
    {
        return [
            ['2010-01-01 00:09:00', 101],
            ['2010-01-01 01:09:00', 113],
        ];
    }

    /**
     * @dataProvider provideGetNineBitMinuteOfDay
     * @param string $date
     * @param int $bits
     */
    public function testGetNineBitMinuteOfDay($date, $bits)
    {
        $obj = new Date($date);
        $this->assertSame($bits, $obj->getNineBitMinuteOfDay());
    }

}
