<?php
declare(strict_types=1);

namespace Assertis\Util;

use InvalidArgumentException;
use Memcached;

/**
 * This class should be used when we will migrate to PHP 7.2
 *
 * @author Łukasz Nowak <lukasz.nowak@assertis.co.uk>
 * @author Bartłomiej Olewiński <bartlomiej.olewinski@assertis.co.uk>
 * Class DistributedMemcachedMutex
 * @package Assertis\Util
 */
class DistributedMemcachedMutex extends DistributedMutex
{
    const MEMCACHE_RESPOND_ERR_MSG = 'Server %s not responding.';

    /**
     * DistributedMemcachedMutex constructor.
     * @param Memcached $memcached
     */
    public function __construct(Memcached $memcached)
    {
        $this->memcache = $memcached;
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
        $this->assertServersAddedToMemcachedAndResponding();

        if (false === $this->memcache->add($name, 1, $expirationTimeInSeconds)) {
            throw new AlreadyLockedException($name);
        }
    }

    protected function assertServersAddedToMemcachedAndResponding()
    {
        $serverVersions = $this->memcache->getversion();

        if (count($serverVersions) == 0) {
            throw new InvalidArgumentException(self::MEMCACHE_ERR_MSG);
        }

        foreach ($serverVersions as $server => $version) {
            if ($version == '255.255.255') { //https://secure.php.net/manual/en/memcached.getversion.php#111539
                throw new InvalidArgumentException(sprintf(self::MEMCACHE_RESPOND_ERR_MSG, $server));
            }
        }
    }
}
