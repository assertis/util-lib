<?php

namespace Assertis\Util;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 *
 * List of AmountPence objects
 */
class AmountPenceList extends ObjectList
{
    /**
     * Return true if the value is acceptable for this list. Typically something like:
     *   return $value instanceof MyClass
     *
     * @param mixed $value
     * @return boolean
     */
    public function accepts($value)
    {
        return $value instanceof AmountPence;
    }
}
