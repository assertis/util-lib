<?php
declare(strict_types=1);

namespace Assertis\Util;

use JsonSerializable;

/**
 * Not all currencies has "pence" as a "decile part".
 * This class is here only to have correct name.
 *
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class AmountMoney implements JsonSerializable
{
    const FORMAT = '%01.2f';

    /**
     * @var int
     */
    protected $value;

    /**
     * @param string $amount
     * @return AmountMoney
     */
    public static function fromHumanReadableString(string $amount): AmountMoney
    {
        $amount = str_replace(',', '', $amount);
        $parts = explode('.', $amount);

        $big = intval($parts[0]);
        $small = count($parts) > 1 ? intval(substr($parts[1].'00', 0, 2)) : 0;

        return new static($big * 100 + $small);
    }

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        $this->value = (int)$value;
    }

    /**
     * @param AmountMoney $amount
     * @return AmountPence
     */
    public function minus(AmountMoney $amount): AmountMoney
    {
        return new static($this->value - $amount->getValue());
    }

    /**
     * @param AmountMoney $amount
     * @return AmountMoney
     */
    public function plus(AmountMoney $amount): AmountMoney
    {
        return new static($this->value + $amount->getValue());
    }

    /**
     * @param AmountMoney $amount
     * @return bool
     */
    public function equals(AmountMoney $amount): bool
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