<?php

namespace Assertis\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class StrTest extends TestCase
{
    /**
     * @return array
     */
    public static function provideUcwords(): array
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
     * @param string $input
     * @param string $expected
     */
    #[DataProvider('provideUcwords')]
    public function testUcwords($input, $expected)
    {
        $this->assertSame($expected, Str::ucwords($input));
    }

    public function testSubstrReturnsEmptyStringWhenEmptyStringPassed()
    {
        $this->assertSame('', Str::substr(''));
    }

    public function testSubstrReturnsSubstringWhenLongerStringPassedWithShorteningConstraints()
    {
        $this->assertSame('super', Str::substr('supermegalongtext', 0, 5));
    }

    public function testSubstrReturnsShorterSubstringWhenSomeStringPassedWithShorteningConstraintsGettingOutOfBand()
    {
        $this->assertSame('ext', Str::substr('supermegalongtext', 14, 5));
    }

    public function testSubstrReturnsEmptyStringWhenConstraintsOutOfBoundPassed()
    {
        $this->assertSame('', Str::substr('supermegalongtext', 17, 5));
    }

    public function testSubstrReturnsRestOfString_whenOnlyStartParameterPassed()
    {
        $this->assertSame('longtext', Str::substr('supermegalongtext', 9));
    }

    /**
     * @return array
     */
    public static function provideWrap(): array
    {
        return [
            [ 'Abc def ghi jkl.', 6, "Abc def\nghi\njkl." ],
            [ "Abc def.\nGh ijk.", 6, "Abc\ndef.\nGh ijk." ],
        ];
    }

    /**
     * @param string $input
     * @param int $perLine
     * @param string $expected
     */
    #[DataProvider('provideWrap')]
    public function testWrap($input, $perLine, $expected)
    {
        $this->assertSame($expected, Str::wrap($input, $perLine));

    }
}
