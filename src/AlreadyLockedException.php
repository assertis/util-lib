<?php

namespace Assertis\Util;

use Exception;
use RuntimeException;

/**
 * Exception thrown when lock with key exists
 */
class AlreadyLockedException extends RuntimeException
{
    /**
     * @var string
     */
    private $lockingKey;

    /**
     * @param string $lockingKey
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($lockingKey, $code = 0, Exception $previous = null)
    {
        parent::__construct("Lock with key {$lockingKey} exists.", $code, $previous);
        $this->lockingKey = $lockingKey;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->lockingKey;
    }
}
