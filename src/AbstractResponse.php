<?php

namespace Assertis\Util;

use JsonSerializable;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractResponse implements JsonSerializable
{
    /**
     * Return response converted to array
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Serialize to json.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
