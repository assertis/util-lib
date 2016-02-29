<?php

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;
use TypedMapAlwaysAccept;
use TypedMapNeverAccept;
use TypedMapNeverAcceptKey;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TypedMapTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $key = 'foo';
        $value = 'bar';

        $map = new TypedMapAlwaysAccept([$key => $value]);

        $this->assertTrue($map->has($key));
        $this->assertSame($value, $map[$key]);

        $this->setExpectedException(InvalidArgumentException::class);
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

    public function testOffsetSetRefused()
    {
        $this->setExpectedException(InvalidArgumentException::class);

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

        $this->setExpectedException(InvalidArgumentException::class);
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
        $this->setExpectedException(InvalidArgumentException::class);

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
}
