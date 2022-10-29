<?php

namespace Assertis\Util;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class DateList extends ObjectList
{

    /**
     * Return true if the value is acceptable for this list. Typically something like:
     *   return $value instanceof MyClass
     *
     * @param mixed $value
     * @return boolean
     */
    public function accepts($value): bool
    {
        return $value instanceof Date;
    }
}
