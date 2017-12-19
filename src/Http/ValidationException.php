<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class ValidationException extends RequestException
{
    const ERROR_CODE = 'VALIDATION';

    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct('Input data did not pass validation', Response::HTTP_BAD_REQUEST);
        
        $this->violations = $violations;
    }

    /**
     * @param array $errors
     * @throws ValidationException
     */
    public static function fromArray(array $errors)
    {
        $list = new ConstraintViolationList();

        foreach ($errors as $property => $message) {
            $list->add(new ConstraintViolation($message, null, [], null, $property, null));
        }

        throw new ValidationException($list);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $out = [];

        /** @var ConstraintViolation $violation */
        foreach ($this->violations as $violation) {
            $path = $this->parsePropertyPathToDotsNotation($violation->getPropertyPath());
            $out[$path] = $violation->getMessage();
        }

        return $out;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function parsePropertyPathToDotsNotation($path): string
    {
        $dottedPath = str_replace('][', '.', $path);
        $trimmedPath = preg_replace('/^\[(.+)\]$/', '$1', $dottedPath);

        return ($trimmedPath === null) ? $path : $trimmedPath;
    }
}
