<?php

namespace Assertis\Util;

use ArrayIterator;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TypedMapIterator extends ArrayIterator
{
    private array $keys;

    public function __construct(array $keys, array $values, int $flags)
    {
        parent::__construct($values, $flags);

        $this->keys = $keys;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->keys[parent::key()];
    }
}
