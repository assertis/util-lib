<?php
declare(strict_types = 1);

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
     * @param string $amount
     * @return AmountPence
     */
    public static function fromHumanReadableString(string $amount): AmountPence
    {
        $amount = str_replace(',', '', $amount);
        $parts = explode('.', $amount);

        $big = intval($parts[0]);
        $small = count($parts) > 1 ? intval(substr($parts[1].'00', 0, 2)) : 0;

        return new self($big * 100 + $small);
    }

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
    public function minus(AmountPence $amount): AmountPence
    {
        return new self($this->value - $amount->getValue());
    }

    /**
     * @param AmountPence $amount
     * @return AmountPence
     */
    public function plus(AmountPence $amount): AmountPence
    {
        return new self($this->value + $amount->getValue());
    }

    /**
     * @param AmountPence $amount
     * @return bool
     */
    public function equals(AmountPence $amount): bool
    {
        return $this->getValue() === $amount->getValue();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(self::FORMAT, $this->value / 100);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
