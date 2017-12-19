<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class UnauthorizedException extends RequestException
{
    const ERROR_CODE = 'UNAUTHORIZED';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
