<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class BadRequestException extends RequestException
{
    const ERROR_CODE = 'BAD-REQUEST';

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
}
