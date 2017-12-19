<?php
declare(strict_types=1);

namespace Assertis\Util\Input;

use Assertis\Util\Http\ValidationException;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class InputFactory
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $className
     * @param array $data
     * @return InputInterface
     * @throws ValidationException
     */
    public function fromArray(string $className, array $data): InputInterface
    {
        if (!is_a($className, InputInterface::class, true)) {
            throw new InvalidArgumentException(sprintf(
                'Class %s does not implement %s',
                $className,
                InputInterface::class
            ));
        }

        /** @var Constraint $constraint */
        $constraint = call_user_func([$className, 'getConstraint']);

        /** @var ConstraintViolationListInterface $violations */
        $violations = $this->validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }

        return call_user_func([$className, 'fromArray'], [$data]);
    }
}
