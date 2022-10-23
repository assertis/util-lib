<?php

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypedMapAlwaysAccept;
use TypedMapNeverAccept;
use TypedMapNeverAcceptKey;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TypedMapTest extends TestCase
{
    public function testConstructor()
    {
        $key = 'foo';
        $value = 'bar';

        $map = new TypedMapAlwaysAccept([$key => $value]);

        $this->assertTrue($map->has($key));
        $this->assertTrue(isset($map[$key]));
        $this->assertSame($value, $map[$key]);

        $this->expectException(InvalidArgumentException::class);
        new TypedMapNeverAccept([$key => $value]);
    }

    public function testOffsetSetAccepted()
    {
        $map = new TypedMapAlwaysAccept();
        $key = 'foo';
        $value = 'bar';

        $this->assertFalse($map->has($key));

        $map[$key] = $value;

        $this->assertTrue($map->has($key));
        $this->assertSame($value, $map[$key]);
    }

    public function testSetAccepted()
    {
        $map = new TypedMapAlwaysAccept();
        $key = 'foo';
        $value = 'bar';

        $this->assertFalse($map->has($key));

        $map->set($key, $value);

        $this->assertTrue($map->has($key));
        $this->assertSame($value, $map[$key]);
    }

    public function testOffsetSetRefused()
    {
        $this->expectException(InvalidArgumentException::class);

        $map = new TypedMapNeverAccept();
        $map['foo'] = 'bar';
    }

    public function testGet()
    {
        $key = 'foo';
        $invalidKey = 'invalid';

        $value = 'bar';
        $defaultValue = 'baz';

        $map = new TypedMapAlwaysAccept([$key => $value]);

        $this->assertSame($value, $map->get($key));
        $this->assertSame($defaultValue, $map->get($invalidKey, $defaultValue));

        $this->expectException(InvalidArgumentException::class);
        $map->get($invalidKey);
    }

    public function testClear()
    {
        $key = 'foo';
        $value = 'bar';

        $map = new TypedMapAlwaysAccept([$key => $value]);
        $map->clear();

        $this->assertFalse($map->has($key));
    }

    public function testOffsetSetKeyRefused()
    {
        $this->expectException(InvalidArgumentException::class);

        $map = new TypedMapNeverAcceptKey();
        $map['foo'] = 'bar';
    }

    public function testObjectAsKey()
    {
        $key = new stdClass('foo');
        $value = new stdClass('bar');

        $map = new TypedMapAlwaysAccept();
        $map[$key] = $value;

        $this->assertTrue($map->has($key));
        $this->assertSame($value, $map[$key]);
        $this->assertSame($value, $map->get($key));
    }

    public function testGetKeysGetValues()
    {
        $key1 = new stdClass('foo');
        $key2 = new stdClass('bar');
        $key3 = new stdClass('baz');

        $value1 = 'FOO';
        $value2 = new stdClass('BAR');
        $value3 = null;

        $map = new TypedMapAlwaysAccept();
        $map->set($key1, $value1);
        $map->set($key2, $value2);
        $map->set($key3, $value3);

        $this->assertSame([$key1, $key2, $key3], $map->getKeys());
        $this->assertSame([$value1, $value2, $value3], $map->getValues());
    }

    public function testUnset()
    {
        $key1 = 'foo';
        $key2 = new stdClass('bar');

        $map = new TypedMapAlwaysAccept();
        $map->set($key1, true);
        $map->set($key2, true);

        $this->assertTrue(isset($map[$key1]));
        $this->assertTrue($map->has($key2));

        unset($map[$key1]);

        $this->assertFalse(isset($map[$key1]));
        $this->assertFalse($map->has($key1));
        $this->assertTrue($map->has($key2));

        $map->offsetUnset($key2);

        $this->assertFalse(isset($map[$key1]));
        $this->assertFalse($map->has($key1));
        $this->assertFalse($map->has($key2));
    }

    public function testSerialization()
    {
        $key1 = 'foo';
        $value1 = new stdClass('Foo');
        $key2 = new stdClass('Bar');
        $value2 = 'bar';

        $map = new TypedMapAlwaysAccept();
        $map->set($key1, $value1);
        $map->set($key2, $value2);

        $expected = $map->toArray();
        $actual = TypedMapAlwaysAccept::fromArray($expected)->toArray();

        $this->assertSame($expected, $actual);
    }

    public function testForeach()
    {
        $key1 = new stdClass('foo');
        $value1 = 'Foo';
        $key2 = new stdClass('bar');
        $value2 = 'Bar';

        $map = new TypedMapAlwaysAccept();
        $map->set($key1, $value1);
        $map->set($key2, $value2);

        $counter = 0;
        foreach ($map as $key => $value) {
            $counter++;
            $this->assertInstanceOf(stdClass::class, $key);
            $this->assertIsString($value);
        }

        $this->assertSame(2, $counter);
    }
}
