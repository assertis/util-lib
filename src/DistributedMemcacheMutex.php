<?php

namespace Assertis\Util;

use Memcache;

/**
 * Utility methods for manipulating distributed locks using memcache
 */
class DistributedMemcacheMutex
{
    const FIVE_MINUTES_SECONDS = 300;

    /**
     * @var Memcache
     */
    private $memcache;

    /**
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * @param string $name
     * @param int $expirationTimeInSeconds
     *
     * @throws AlreadyLockedException
     */
    public function lock($name, $expirationTimeInSeconds = self::FIVE_MINUTES_SECONDS)
    {
        if (false === $this->memcache->add($name, 1, false, $expirationTimeInSeconds)) {
            throw new AlreadyLockedException($name);
        }
    }

    /**
     * @param string $name
     */
    public function unlock($name)
    {
        $this->memcache->delete($name);
    }
}
