<?php

namespace Assertis\Util;

use JsonSerializable;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class AmountPence implements JsonSerializable
{
    const FORMAT = '&pound;%01.2f';

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        $this->value = (int)$value;
    }

    /**
     * @param AmountPence $amount
     * @return AmountPence
     */
    public function minus(AmountPence $amount)
    {
        return new self($this->value - $amount->getValue());
    }

    /**
     * @param AmountPence $amount
     * @return bool
     */
    public function equals(AmountPence $amount)
    {
        return $this->getValue() === $amount->getValue();
    }

    /**
     * @return int
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
        return sprintf(self::FORMAT, $this->value / 100);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
