<?php

namespace Assertis\Util;

use ArrayIterator;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TypedMapIterator extends ArrayIterator
{
    /**
     * @var array
     */
    private $keys;

    /**
     * @param array $keys
     * @param array $values
     * @param int $flags
     */
    public function __construct(array $keys, array $values, $flags)
    {
        parent::__construct($values, $flags);
        $this->keys = $keys;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->keys[parent::key()];
    }
}
