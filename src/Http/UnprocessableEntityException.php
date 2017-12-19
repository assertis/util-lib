<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Angulski <mateusz.angulski@assertis.co.uk>
 */
class UnprocessableEntityException extends RequestException
{
    const ERROR_CODE = 'UNPROCESSABLE-ENTITY';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
