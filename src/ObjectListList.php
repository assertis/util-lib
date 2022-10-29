<?php

namespace Assertis\Util;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ObjectListList extends ObjectList
{
    /**
     * @param mixed $value
     * @return boolean
     */
    public function accepts($value): bool
    {
        return $value instanceof ObjectList;
    }
}
