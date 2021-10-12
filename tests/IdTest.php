<?php

namespace Assertis\Util;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class IdTest extends TestCase
{
    public function testSerialization()
    {
        $value = 1;

        /** @var Id|MockObject $id */
        $id = $this->getMockForAbstractClass(Id::class, [$value]);

        $this->assertSame((string)$value, $id->getValue());
        $this->assertSame((string)$value, (string)$id);
        $this->assertSame((string)$value, $id->toArray());
        $this->assertSame(json_encode((string)$value), json_encode($id));
    }

    public function testMatches()
    {
        /** @var Id|MockObject $idA */
        $idA = $this->getMockForAbstractClass(Id::class, ['A']);
        /** @var Id|MockObject $idA2 */
        $idA2 = $this->getMockForAbstractClass(Id::class, ['A']);
        /** @var Id|MockObject $idB */
        $idB = $this->getMockForAbstractClass(Id::class, ['B']);

        $this->assertTrue($idA->matches($idA));
        $this->assertTrue($idA->matches($idA2));
        $this->assertFalse($idA->matches($idB));
    }
}
