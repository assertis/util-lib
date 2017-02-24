<?php
declare(strict_types = 1);

namespace Assertis\Util;

use ObjectListAlwaysAccept;
use ObjectListNeverAccept;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class ObjectListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionIfElementNotAccepted()
    {
        $stub = new ObjectListNeverAccept();
        $stub->__construct(['not-accepted']);
    }

    public function testConstructor()
    {
        $values = ['value-1', 'value-2'];
        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testAppend()
    {
        $stub = new ObjectListAlwaysAccept();

        $value = 'value-1';
        $stub->append($value);
        $this->assertSame([$value], $stub->getArrayCopy());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAppendThrowsExceptionIfValueNotAccepted()
    {
        $stub = new ObjectListNeverAccept();
        $stub->append('not-accepted');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSetThrowsExceptionIfValueNotAccepted()
    {
        $stub = new ObjectListNeverAccept();
        $stub[0] = 'not-accepted';
    }

    /**
     * @return array
     */
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
     * @param array $values
     * @param int $count
     */
    public function testGetAllPermutations($values, $count)
    {
        $stub = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame($limit, count($stub->getAllPermutations($limit)));
    }

    public function testGetFirstAndLast()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame(1, $stub->getFirst());
        $this->assertSame(3, $stub->getLast());
    }

    public function testPop()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);

        $map = function ($value) {
            return $value * 2;
        };

        $this->assertSame([2, 4, 6, 8, 10], $stub->map($map));
        $this->assertSame($values, $stub->getArrayCopy());
    }

    public function testEach()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);
        $stub2 = new ObjectListAlwaysAccept($values);

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

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame(true, $stub->contains($values[0]));
        $this->assertSame(false, $stub->contains(new stdClass()));
    }

    public function testGroup()
    {
        $values = [1, 2, 3, 4, 5];

        $stub = new ObjectListAlwaysAccept($values);
        $groups = $stub->group(function ($value) {
            return $value % 2;
        });

        $this->assertSame(2, count($groups));
    }

    /**
     * @return array
     */
    public function provideSlice()
    {
        return [
            [0, 1, 1, 1, 1],
            [1, 2, 2, 2, 3],
            [4, 9, 1, 5, 5],
            [4, null, 1, 5, 5],
            [5, null, 0, false, false],
        ];
    }

    /**
     * @dataProvider provideSlice
     * @param int $offset
     * @param int|null $length
     * @param int $expectedCount
     * @param int $expectedFirst
     * @param int $expectedLast
     */
    public function testSlice($offset, $length, $expectedCount, $expectedFirst, $expectedLast)
    {
        $values = [1, 2, 3, 4, 5];

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame(count($values), $stub->count());

        $slice = $stub->slice($offset, $length);
        $this->assertSame($expectedCount, $slice->count());
        $this->assertSame($expectedFirst, $slice->getFirst());
        $this->assertSame($expectedLast, $slice->getLast());

        $this->assertSame(count($values), $stub->count());
    }

    /**
     * @return array
     */
    public function provideSum()
    {
        return [
            [[1, 2, 3], null, 6],
            [[1, 2, 3], 0.0, 6.0],
            [[1.0, 2, 3], null, 6.0],
            [[1.0, 2, 3], 0, 6.0],
        ];
    }

    /**
     * @dataProvider provideSum
     * @param array $values
     * @param mixed $default
     * @param int|float $expected
     */
    public function testSum($values, $default, $expected)
    {
        $stub = new ObjectListAlwaysAccept($values);
        $return = function ($value) {
            return $value;
        };

        $this->assertSame($expected, $stub->sum($return, $default));
    }

    public function testFind()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame(1, $stub->find(function ($value) {
            return $value === 1;
        }));

        $this->assertSame(null, $stub->find(function ($value) {
            return $value === 4;
        }));
    }

    public function testGet()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

        $this->assertSame(1, $stub->get(function ($value) {
            return $value === 1;
        }));

        $this->setExpectedException(ObjectListElementNotFoundException::class);
        $stub->get(function ($value) {
            return $value === 4;
        });
    }

    public function testSerialization()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

        $original = $stub->toArray();
        $transformed = ObjectListAlwaysAccept::fromArray($original)->toArray();

        $this->assertSame($original, $transformed);
    }

    public function testUniqueFor()
    {
        $values = [1, 2, 3];

        $stub = new ObjectListAlwaysAccept($values);

        $resultOne = $stub->uniqueFor(function () {
            return 1;
        });
        $resultAll = $stub->uniqueFor(function ($value) {
            return $value;
        });

        $this->assertSameSize($stub, $resultAll);

        $this->assertSame(1, $resultOne->count());
        $this->assertSame(1, $resultOne->getFirst());
    }

    public function testExclude()
    {
        $values = [1, 2, 3];
        $excluded = [1];

        $stub = new ObjectListAlwaysAccept($values);
        $excludedList = new ObjectListAlwaysAccept($excluded);

        $actual = $stub->exclude($excludedList);

        $this->assertSame(2, $actual->count());
        $this->assertSame([1 => 2, 2 => 3], $actual->toArray());
    }

    public function testIntersect()
    {
        $values = [1, 2, 3];
        $retained = [1, 3];

        $stub = new ObjectListAlwaysAccept($values);
        $retainedList = new ObjectListAlwaysAccept($retained);

        $actual = $stub->intersect($retainedList);

        $this->assertSame(2, $actual->count());
        $this->assertSame([0 => 1, 2 => 3], $actual->toArray());
    }

    public function testHasMatching()
    {
        $list = new ObjectListAlwaysAccept([1,2,3,4,5]);
        $this->assertTrue($list->hasMatching(function($int){
            return $int === 4;
        }));

        $this->assertFalse($list->hasMatching(function($int){
            return $int === 6;
        }));
    }

}
