<?php

namespace Assertis\Util;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TypedMapAssociativeSerializationTraitTest extends TestCase
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

		$map = new class($keys, $values) {
			use TypedMapAssociativeSerializationTrait;

			public function __construct(public array $keys, public array $values) {}

			public function serializeKey($key): mixed { return $key; }
			public function serializeValue($value): mixed { return $value; }
			public function getArrayCopy(): array { return $this->values; }
			public function getKey($keyId): mixed { return $this->keys[$keyId]; }
			public function set($key, $value): void { $this->values[$key] = $value; }
		};

        $this->assertSame($expected, $map->toArray());
    }
}
