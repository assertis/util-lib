<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testSerialization(): void
    {
        $value = 1;
        $id = new TestableId($value);

        $this->assertSame((string)$value, $id->getValue());
        $this->assertSame((string)$value, (string)$id);
        $this->assertSame((string)$value, $id->toArray());
        $this->assertSame(json_encode((string)$value), json_encode($id));
    }

    public function testMatches(): void
    {
        $idA = new TestableId('A');
        $idA2 = new TestableId('A');
        $idB = new TestableId('B');

        $this->assertTrue($idA->matches($idA));
        $this->assertTrue($idA->matches($idA2));
        $this->assertFalse($idA->matches($idB));
    }
}

/**
 * Minimal testable implementation of the abstract Id class.
 */
class TestableId extends Id
{
    // Assuming the Id constructor handles setting the value and getValue()/matches() are concrete
}
