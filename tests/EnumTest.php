<?php
declare(strict_types=1);

namespace Assertis\Util;

use BadMethodCallException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class EnumTest extends TestCase
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
        ], $enum::values());
    }

    public function testValidateValue()
    {
        new TestEnum(TestEnum::STRING);
        new TestEnum(TestEnum::NULL);

        $this->expectException(InvalidArgumentException::class);
        new TestEnum('FOO');

        $this->expectException(InvalidArgumentException::class);
        new TestEnum(-1);
    }

    public function testChild()
    {
        new TestChildEnum(TestChildEnum::CHILD);
        new TestChildEnum(TestChildEnum::STRING);
        self::assertTrue(true);
    }

    public function testStaticConstructor()
    {
        TestEnum::STRING();
        TestChildEnum::STRING();
        TestChildEnum::CHILD();
        self::assertTrue(true);
    }

    public function testStaticConstructorFail()
    {
        $this->expectException(BadMethodCallException::class);

        TestEnum::NOTHING();
    }
}

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */

/**
 * @method static STRING
 */
class TestEnum extends Enum
{
    public const STRING = 'A';
    public const EMPTY_STRING = '';
    public const ZERO = 0;
    public const TEN = 10;
    public const TRUE = true;
    public const FALSE = false;
    public const NULL = null;
}

/**
 * @method static CHILD
 */
class TestChildEnum extends TestEnum
{
    public const CHILD = 'child';
}
