<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;
use stdClass;
use TestObjectList;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class ObjectListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param array $values
     * @return ObjectList
     */
    private function getMockList($values)
    {
        return new TestObjectList($values);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionIfElementNotAccepted()
    {
        $stub = $this->getMockBuilder(ObjectList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('accepts')
            ->willReturn(false);

        $stub->__construct(['not-accepted']);
    }

    public function testConstructor()
    {
        $stub = $this->getMockForAbstractClass(ObjectList::class);
        $stub->expects($this->any())
            ->method('accepts')
            ->willReturn(true);

        $values = ['value-1', 'value-2'];

        $stub->__construct($values);
        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testAppend()
    {
        $stub = $this->getMockForAbstractClass(ObjectList::class);
        $stub->expects($this->any())
            ->method('accepts')
            ->willReturn(true);

        $value = 'value-1';
        $stub->append($value);
        $this->assertSame([$value], $stub->getArrayCopy());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAppendThrowsExceptionIfValueNotAccepted()
    {
        $stub = $this->getMockForAbstractClass(ObjectList::class);
        $stub->expects($this->any())
            ->method('accepts')
            ->willReturn(false);

        $value = 'not-accepted';
        $stub->append($value);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSetThrowsExceptionIfValueNotAccepted()
    {
        $stub = $this->getMockForAbstractClass(ObjectList::class);
        $stub->expects($this->any())
            ->method('accepts')
            ->willReturn(false);

        $stub[0] = 'not-accepted';
    }

    public function provideGetAllPermutations()
    {
        return [
            [[], 0],
            [[1], 1],
            [[1, 2], 2],
            [[1, 2, 3], 6],
            [[1, 2, 3, 4], 24],
            [[1, 2, 3, 4, 5], 120],
        ];
    }

    /**
     * @dataProvider provideGetAllPermutations
     */
    public function testGetAllPermutations($values, $count)
    {
        $stub = $this->getMockList($values);

        $perms = $stub->getAllPermutations();

        $this->assertSame($count, count($perms));

        if (count($perms) > 0) {
            $this->assertSame(count($values), count($perms[0]));
        }

        if (count($perms) > 1) {
            $this->assertNotEquals($perms[0]->getArrayCopy(), $perms[1]->getArrayCopy());
        }
    }

    public function testGetAllPermutationsWithLimit()
    {
        $values = [1, 2, 3, 4, 5];
        $limit = 3;

        $stub = $this->getMockList($values);

        $this->assertSame($limit, count($stub->getAllPermutations($limit)));
    }

    public function testGetFirstAndLast()
    {
        $values = [1, 2, 3];

        $stub = $this->getMockList($values);

        $this->assertSame(1, $stub->getFirst());
        $this->assertSame(3, $stub->getLast());
    }

    public function testPop()
    {
        $values = [1, 2, 3];

        $stub = $this->getMockList($values);

        $this->assertSame(3, $stub->count());

        $this->assertSame(3, $stub->pop());
        $this->assertSame(2, $stub->count());

        $this->assertSame(2, $stub->pop());
        $this->assertSame(1, $stub->count());

        $this->assertSame(1, $stub->pop());
        $this->assertSame(0, $stub->count());
    }

    public function testCountAndPopMatching()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);

        $matchOdd = function ($value) {
            return $value % 2;
        };

        $this->assertSame(3, $stub->countMatching($matchOdd));

        $this->assertSame(1, $stub->popMatching($matchOdd));
        $this->assertSame(3, $stub->popMatching($matchOdd));
        $this->assertSame(5, $stub->popMatching($matchOdd));
        $this->assertSame(null, $stub->popMatching($matchOdd));

        $this->assertSame(2, $stub->count());
    }

    public function testMap()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);

        $map = function ($value) {
            return $value * 2;
        };

        $this->assertSame([2, 4, 6, 8, 10], $stub->map($map));
        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testEach()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);

        $total = 0;
        $stub->each(function ($value) use (&$total) {
            $total += $value;
        });

        $this->assertSame(15, $total);
        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testReduce()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);

        $total = $stub->reduce(function ($carry, $value) {
            return $carry + $value;
        });

        $totalWithInitial = $stub->reduce(function ($carry, $value) {
            return $carry + $value;
        }, 10);

        $this->assertSame(15, $total);
        $this->assertSame(25, $totalWithInitial);
        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testAppendAll()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);
        $stub2 = $this->getMockList($values);

        $this->assertSame(count($values), $stub->count());
        $this->assertSame(count($values), $stub2->count());

        $this->assertSame($stub, $stub->appendAll($stub2));

        $this->assertSame(count($values) + $stub2->count(), $stub->count());
    }

    public function testContains()
    {
        $values = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $stub = $this->getMockList($values);

        $this->assertSame(true, $stub->contains($values[0]));
        $this->assertSame(false, $stub->contains(new stdClass()));
    }

    public function testGroup()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = $this->getMockList($values);
        $groups = $stub->group(function ($value) {
            return $value % 2;
        });

        $this->assertSame(2, count($groups));
    }
}
