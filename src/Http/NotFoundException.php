<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class NotFoundException extends RequestException
{
    const ERROR_CODE = 'NOT-FOUND';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
