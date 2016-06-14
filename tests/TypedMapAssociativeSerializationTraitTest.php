<?php

namespace Assertis\Util;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TypedMapAssociativeSerializationTraitTest extends PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $expected = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        ];
        
        $keys = array_keys($expected);
        $values = array_values($expected);

        /** @var TypedMapAssociativeSerializationTrait|PHPUnit_Framework_MockObject_MockObject $map */
        $map = $this->getMockForTrait(TypedMapAssociativeSerializationTrait::class);
        $map->expects($this->any())->method('serializeKey')->willReturnArgument(0);
        $map->expects($this->any())->method('serializeValue')->willReturnArgument(0);
        $map->expects($this->once())->method('getArrayCopy')->willReturn($values);
        $map->expects($this->any())->method('getKey')->willReturnCallback(function ($keyId) use ($keys) {
            return $keys[$keyId];
        });

        $this->assertSame($expected, $map->toArray());
    }
}
