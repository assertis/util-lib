<?php

namespace Assertis\Util;

class ObjectListList extends ObjectList
{
    /**
     * @param mixed $value
     * @return boolean
     */
    public function accepts($value)
    {
        return $value instanceof ObjectList;
    }
}
