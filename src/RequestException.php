<?php

namespace Assertis\Util;

use Exception;

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
    abstract public function getHttpCode();

    /**
     * @return string
     */
    abstract public function getSpecificErrorCode();

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return self::ERROR_CODE_PREFIX . $this->getSpecificErrorCode();
    }

    /**
     * @return string
     */
    public static function getUnknownErrorCode()
    {
        return self::ERROR_CODE_PREFIX . self::ERROR_CODE_UNKNOWN;
    }

    /**
     * @return string
     */
    public function getErrorType()
    {
        return self::TYPE_ERROR;
    }

    /**
     * @return bool
     */
    public function isLogged()
    {
        return true;
    }
}
