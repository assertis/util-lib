<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class StringTest extends PHPUnit_Framework_TestCase
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
        $this->assertSame($expected, String::ucwords($input));
    }
}
