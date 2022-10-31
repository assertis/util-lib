<?php

declare(strict_types=1);

namespace Assertis\Util;

use JsonSerializable;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class Id implements JsonSerializable
{
    private ?string $value;

    public function __construct($value)
    {
        $this->value = (string)$value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public function toArray(): string
    {
        return (string)$this;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function matches(Id $otherId): bool
    {
        return $this->value === $otherId->getValue();
    }
}
