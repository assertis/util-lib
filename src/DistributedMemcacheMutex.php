<?php

namespace Assertis\Util;

use InvalidArgumentException;
use Memcache;
use Memcached;

/**
 * Utility methods for manipulating distributed locks using memcache
 */
class DistributedMemcacheMutex
{
    const FIVE_MINUTES_SECONDS = 300;
    const MEMCACHE_ERR_MSG = 'No servers added to memcache connection.';

    /**
     * @var Memcache
     */
    protected $memcache;

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
     * @throws InvalidArgumentException
     * @throws AlreadyLockedException
     */
    public function lock($name, $expirationTimeInSeconds = self::FIVE_MINUTES_SECONDS)
    {
        $this->assertServersAddedToMemcache();
        if (false === $this->memcache->add($name, 1, false, $expirationTimeInSeconds)) {
            throw new AlreadyLockedException($name);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function assertServersAddedToMemcache()
    {
        if ($this->memcache->getversion() === false) {
            throw new InvalidArgumentException(self::MEMCACHE_ERR_MSG);
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
