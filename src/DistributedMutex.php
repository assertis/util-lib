<?php
/**
 * @author Bartłomiej Olewiński <bartlomiej.olewinski@gmail.com>
 */

namespace Assertis\Util;

use Memcache;
use Memcached;

abstract class DistributedMutex
{
    const FIVE_MINUTES_SECONDS = 300;
    const MEMCACHE_ERR_MSG = 'No servers added to memcache connection.';

    /**
     * @var Memcache|Memcached
     */
    protected $memcache;

    abstract public function lock($name, $expirationTimeInSeconds);

    /**
     * @param string $name
     */
    public function unlock($name): void
    {
        $this->memcache->delete($name);
    }
}
