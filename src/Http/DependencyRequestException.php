<?php
declare(strict_types = 1);

/**
 * @author Bartłomiej Olewiński <bartlomiej.olewinski@assertis.co.uk>
 */

namespace Assertis\Util\Http;

use Assertis\Util\RequestException;
use Symfony\Component\HttpFoundation\Response;

class DependencyRequestException extends RequestException
{

    const ERROR_CODE = 'DEPENDENT-REQUEST-FAIL';

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_FAILED_DEPENDENCY;
    }

    /**
     * @return string
     */
    public function getSpecificErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
