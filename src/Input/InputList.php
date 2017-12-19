<?php
declare(strict_types=1);

namespace Assertis\Util\Input;

use Assertis\Util\ObjectList;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
abstract class InputList extends ObjectList implements InputInterface
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return $value instanceof InputInterface;
    }
}
