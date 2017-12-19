<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class AccessDeniedException extends RequestException
{
    const ERROR_CODE = 'ACCESS-DENIED';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
