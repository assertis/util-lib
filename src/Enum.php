<?php
declare(strict_types=1);

namespace Assertis\Util;

use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionClass;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * Class represents enumeration class
 */
abstract class Enum implements JsonSerializable
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
        $this->validateValue($value);

        $this->value = $value;
    }

    public static function values(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }

    public static function __callStatic(string $name, array $arguments): self
    {
        $values = static::values();

        if (!array_key_exists($name, $values)) {
            throw new BadMethodCallException(sprintf(
                'Constant is not defined in class %s: %s',
                static::class,
                $name
            ));
        }

        return new static($values[$name]);
    }

    /**
     * Return true if value is equal enum value
     *
     * @param mixed $value
     * @return bool
     */
    public function equal($value): bool
    {
        return $this->value === $value;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    private function validateValue($value): void
    {
        if (!in_array($value, static::values(), true)) {
            throw new InvalidArgumentException("Bad type [$value] of constant in " . static::class);
        }
    }
}
