<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class StringUtilTest extends PHPUnit_Framework_TestCase
{

    public function provideUcwords()
    {
        return [
            ['one two', 'One Two'],
            ['ONE TWO', 'One Two'],
            ['One Two', 'One Two'],
            ['ONETWO', 'Onetwo'],
            ['OneTwo', 'Onetwo'],
        ];
    }

    /**
     * @dataProvider provideUcwords
     * @param string $input
     * @param string $expected
     */
    public function testUcwords($input, $expected)
    {
        $this->assertSame($expected, StringUtil::ucwords($input));
    }

    public function testSubstrReturnsEmptyStringWhenEmptyStringPassed()
    {
        $this->assertSame('', StringUtil::substr(''));
    }

    public function testSubstrReturnsSubstringWhenLongerStringPassedWithShorteningConstraints()
    {
        $this->assertSame('super', StringUtil::substr('supermegalongtext', 0, 5));
    }

    public function testSubstrReturnsShorterSubstringWhenSomeStringPassedWithShorteningConstraintsGettingOutOfBand()
    {
        $this->assertSame('ext', StringUtil::substr('supermegalongtext', 14, 5));
    }

    public function testSubstrReturnsEmptyStringWhenConstraintsOutOfBoundPassed()
    {
        $this->assertSame('', StringUtil::substr('supermegalongtext', 17, 5));
    }

    public function testSubstrReturnsRestOfString_whenOnlyStartParameterPassed()
    {
        $this->assertSame('longtext', StringUtil::substr('supermegalongtext', 9));
    }

    public function provideWrap()
    {
        return [
            [ 'Abc def ghi jkl.', 6, "Abc def\nghi\njkl." ],
            [ "Abc def.\nGh ijk.", 6, "Abc\ndef.\n\nGh ijk." ],
        ];
    }
    
    /**
     * @dataProvider provideWrap
     * @param string $input
     * @param int $perLine
     * @param string $expected
     */
    public function testWrap($input, $perLine, $expected)
    {
        $input = 'Abc def ghi jkl.';
        $perLine = 6;
        $expected = "Abc def\nghi\njkl.";

        $this->assertSame($expected, StringUtil::wrap($input, $perLine));

    }
}