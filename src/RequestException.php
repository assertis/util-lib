<?php

namespace Assertis\Util;

use Exception;
use Psr\Log\LogLevel;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
abstract class RequestException extends Exception
{
    const ERROR_CODE_PREFIX = 'ERR-';
    const ERROR_CODE_UNKNOWN = 'UNKNOWN';

    const TYPE_ERROR = 'error';
    const TYPE_FAIL = 'fail';
    const TYPE_OK = 'ok';
    const TYPE_SUCCESS = 'success';

    /**
     * @return int
     */
    abstract public function getHttpCode(): int;

    /**
     * @return string
     */
    abstract public function getSpecificErrorCode(): string;

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return self::ERROR_CODE_PREFIX . $this->getSpecificErrorCode();
    }

    /**
     * @return string
     */
    public static function getUnknownErrorCode(): string
    {
        return self::ERROR_CODE_PREFIX . self::ERROR_CODE_UNKNOWN;
    }

    /**
     * @return string
     */
    public function getErrorType(): string
    {
        return self::TYPE_ERROR;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return LogLevel::ERROR;
    }
}
