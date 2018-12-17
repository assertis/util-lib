<?php

namespace Assertis\Util;

use InvalidArgumentException;
use Memcache;

/**
 * Utility methods for manipulating distributed locks using memcache
 */
class DistributedMemcacheMutex extends DistributedMutex
{
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
}
