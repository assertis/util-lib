<?php

namespace Assertis\Util;

use JsonSerializable;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class Id implements JsonSerializable
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @return string
     */
    public function toArray()
    {
        return (string) $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * @param Id $otherId
     * @return bool
     */
    public function matches(Id $otherId)
    {
        return $this->value === $otherId->getValue();
    }
}
