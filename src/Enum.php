<?php

namespace Assertis\Util;

use InvalidArgumentException;
use JsonSerializable;
use ReflectionClass;

/**
 * Class Enum
 * @package Assertis\Util
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * Class represents enumeration class
 */
class Enum implements JsonSerializable
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var array
     */
    private $constants;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->reflection = new ReflectionClass(get_called_class());
        $this->constants = $this->reflection->getConstants();
        $this->validateValue($value);

        $this->value = $value;
    }

    /**
     * Return true if value is equal enum value
     *
     * @param mixed $value
     * @return bool
     */
    public function equal($value)
    {
        return $this->value === $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public static function values()
    {
        $reflection = new ReflectionClass(get_called_class());

        return $reflection->getConstants();
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    private function validateValue($value)
    {
        if (!in_array($value, $this->constants, true)) {
            throw new InvalidArgumentException("Bad type [$value] of constant in " . get_called_class());
        }
    }
}
