<?php
declare(strict_types = 1);

namespace Assertis\Util;


/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class AmountPence extends AmountMoney
{
    const FORMAT = '&pound;%01.2f';
    
    /**
     * @param AmountMoney $amount
     * @return AmountPence
     */
    public function minus(AmountMoney $amount): AmountMoney
    {
        return new self($this->value - $amount->getValue());
    }

    /**
     * @param AmountMoney $amount
     * @return AmountMoney
     */
    public function plus(AmountMoney $amount): AmountMoney
    {
        return new self($this->value + $amount->getValue());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(self::FORMAT, $this->value / 100);
    }

}
