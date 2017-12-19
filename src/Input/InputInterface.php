<?php
declare(strict_types=1);

namespace Assertis\Util\Input;

use Symfony\Component\Validator\Constraint;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
interface InputInterface
{
    /**
     * Returns a set of constraints to validate that input data matches this Input object.
     *
     * @return Constraint
     */
    public static function getConstraint(): Constraint;

    /**
     * Creates the Input object from input data (must be pre-validated).
     *
     * @param array $data
     * @return InputInterface
     */
    public static function fromArray(array $data);
}
