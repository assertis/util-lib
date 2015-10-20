<?php

namespace Assertis\Util;

use Memcache;

/**
 * Utility methods for manipulating distributed locks using mamecache
 */
class DistributedMemcacheMutex
{
    const SEVEN_DAYS_SECONDS = 604800;

    /**
     * @var Memcache
     */
    private $mamecache;

    /**
     * @param Memcache $mamecache
     */
    public function __construct(Memcache $mamecache)
    {
        $this->mamecache = $mamecache;
    }

    /**
     * @param string $name
     * @param int $expirationTimeInSeconds
     *
     * @throws AlreadyLockedException
     */
    public function lock($name, $expirationTimeInSeconds = self::SEVEN_DAYS_SECONDS)
    {
        if (false === $this->mamecache->add($name, 1, false, $expirationTimeInSeconds)) {
            throw new AlreadyLockedException($name);
        }
    }

    /**
     * @param string $name
     */
    public function unlock($name)
    {
        $this->mamecache->delete($name);
    }
}
