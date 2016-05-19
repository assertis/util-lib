<?php

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class EnumTest extends PHPUnit_Framework_TestCase
{
    public function testEqual()
    {
        $enum = new TestEnum(TestEnum::FALSE);

        $this->assertTrue($enum->equal(TestEnum::FALSE));
        $this->assertFalse($enum->equal(TestEnum::ZERO));
        $this->assertFalse($enum->equal(TestEnum::EMPTY_STRING));
        $this->assertFalse($enum->equal(TestEnum::NULL));
    }

    public function testGetValue()
    {
        $enum = new TestEnum(TestEnum::STRING);

        $this->assertSame(TestEnum::STRING, $enum->getValue());
    }

    public function testToString()
    {
        $this->assertSame('A', (string)new TestEnum(TestEnum::STRING));
        $this->assertSame('0', (string)new TestEnum(TestEnum::ZERO));
        $this->assertSame('1', (string)new TestEnum(TestEnum::TRUE));
        $this->assertSame('', (string)new TestEnum(TestEnum::FALSE));
        $this->assertSame('', (string)new TestEnum(TestEnum::NULL));
    }

    public function testJsonEncode()
    {
        $this->assertSame('"A"', json_encode(new TestEnum(TestEnum::STRING)));
        $this->assertSame('0', json_encode(new TestEnum(TestEnum::ZERO)));
        $this->assertSame('true', json_encode(new TestEnum(TestEnum::TRUE)));
        $this->assertSame('false', json_encode(new TestEnum(TestEnum::FALSE)));
        $this->assertSame('null', json_encode(new TestEnum(TestEnum::NULL)));
    }

    public function testValues()
    {
        $enum = new TestEnum(TestEnum::STRING);
        $this->assertSame([
            'STRING'       => 'A',
            'EMPTY_STRING' => '',
            'ZERO'         => 0,
            'TEN'          => 10,
            'TRUE'         => true,
            'FALSE'        => false,
            'NULL'         => null,
        ], $enum->values());
    }
    
    public function testValidateValue()
    {
        new TestEnum(TestEnum::STRING);
        new TestEnum(TestEnum::NULL);

        $this->setExpectedException(InvalidArgumentException::class);
        new TestEnum('FOO');

        $this->setExpectedException(InvalidArgumentException::class);
        new TestEnum(-1);
    }
}

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TestEnum extends Enum
{
    const STRING = 'A';
    const EMPTY_STRING = '';
    const ZERO = 0;
    const TEN = 10;
    const TRUE = true;
    const FALSE = false;
    const NULL = null;
}
