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
        $this->value = (string) $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
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
    public function toArray(): string
    {
        return (string) $this;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @param Id $otherId
     * @return bool
     */
    public function matches(Id $otherId): bool
    {
        return $this->value === $otherId->getValue();
    }
}
