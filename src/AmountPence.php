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
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(self::FORMAT, $this->value / 100);
    }

}
