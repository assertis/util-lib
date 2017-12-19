<?php
declare(strict_types=1);

namespace Assertis\Util\Input;

use Symfony\Component\Validator\Constraint;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TestInput implements InputInterface
{
    public static $constraint;
    public static $fromArrayCallback;

    /**
     * @return Constraint
     */
    public static function getConstraint(): Constraint
    {
        return static::$constraint;
    }

    /**
     * @param array $data
     * @return InputInterface|static
     */
    public static function fromArray(array $data)
    {
        if (static::$fromArrayCallback) {
            (static::$fromArrayCallback)($data);
        }

        return new static;
    }
}
