<?php

namespace Assertis\Util;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 *
 * @template T of object
 * @extends ObjectList<ObjectList<T>>
 */
class ObjectListList extends ObjectList
{
    /**
     * @param ObjectList<T> $value
     */
    public function accepts($value): bool
    {
        return $value instanceof ObjectList;
    }
}
