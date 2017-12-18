<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConflictException extends RequestException
{
    const ERROR_CODE = 'CONFLICT';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_CONFLICT;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
