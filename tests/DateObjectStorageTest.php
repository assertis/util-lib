<?php

namespace Assertis\Util;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class DateObjectStorageTest extends TestCase
{

    public function testOffsetSetGet()
    {
        $date = new Date("2016-12-31");
        $storage = new DateObjectStorage();
        $storage[$date] = "sample value";

        $this->assertEquals("sample value", $storage[$date]);
        $this->assertEquals("sample value", $storage->offsetGet($date));
    }

    public function testOffsetSetThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $storage = new DateObjectStorage();
        $storage[new DateTime()] = "sample value";
    }

    public function testAttach()
    {
        $date1 = new Date("2016-12-31");
        $date2 = new Date("2016-11-31");
        $storage = new DateObjectStorage();

        $storage->attach($date1);
        $this->assertCount(1,$storage);

        $storage->attach($date2, "xxx");
        $this->assertEquals("xxx", $storage[$date2]);
    }

    public function testAttachThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $storage = new DateObjectStorage();
        $storage->attach(new DateTime());
    }

    public function testAddAll()
    {
        $additionalStorage = new SplObjectStorage();
        $additionalStorage->attach(new Date());
        $additionalStorage->attach(new Date());
        $storage = new DateObjectStorage();

        $storage->addAll($additionalStorage);
        $this->assertCount(2, $storage);
    }

    public function testAddAllThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $additionalStorage = new SplObjectStorage();
        $additionalStorage->attach(new Date());
        $additionalStorage->attach(new DateTime());
        $storage = new DateObjectStorage();

        $storage->addAll($additionalStorage);
    }
}
