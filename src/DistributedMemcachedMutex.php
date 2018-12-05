<?php
declare(strict_types=1);

namespace Assertis\Util;

use Memcached;

/**
 * This class should be used when we will migrate to PHP 7.2
 * It extends DistributedMemcacheMutex because all other methods like add, remove etc.
 * are available in memcached also so there is no reason to overwrite methods.
 *
 * @author Łukasz Nowak <lukasz.nowak@assertis.co.uk>
 * Class DistributedMemcachedMutex
 * @package Assertis\Util
 */
class DistributedMemcachedMutex extends DistributedMemcacheMutex
{

    /**
     * DistributedMemcachedMutex constructor.
     * @param Memcached $memcached
     */
    public function __construct(Memcached $memcached)
    {
        $this->memcache = $memcached;
    }
}