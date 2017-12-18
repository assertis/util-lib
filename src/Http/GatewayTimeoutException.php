<?php
declare(strict_types=1);

namespace Assertis\Util\Http;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @author Nick Batalov <nikita.batalov@assertis.co.uk>
 */
class GatewayTimeoutException extends ServerErrorException
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Gateway timed out. Your request has been discarded by remote host. Please try again later',
        int $code = Response::HTTP_GATEWAY_TIMEOUT,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
