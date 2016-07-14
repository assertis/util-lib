<?php

namespace Assertis\Util;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class IdTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $value = 1;
        
        /** @var Id|PHPUnit_Framework_MockObject_MockObject $id */
        $id = $this->getMockForAbstractClass(Id::class, [$value]);

        $this->assertSame((string)$value, $id->getValue());
        $this->assertSame((string)$value, (string)$id);
        $this->assertSame((string)$value, $id->toArray());
        $this->assertSame(json_encode((string)$value), json_encode($id));
    }
    
    public function testMatches()
    {
        /** @var Id|PHPUnit_Framework_MockObject_MockObject $idA */
        $idA = $this->getMockForAbstractClass(Id::class, ['A']);
        /** @var Id|PHPUnit_Framework_MockObject_MockObject $idA2 */
        $idA2 = $this->getMockForAbstractClass(Id::class, ['A']);
        /** @var Id|PHPUnit_Framework_MockObject_MockObject $idB */
        $idB = $this->getMockForAbstractClass(Id::class, ['B']);

        $this->assertTrue($idA->matches($idA));
        $this->assertTrue($idA->matches($idA2));
        $this->assertFalse($idA->matches($idB));
    }
}
