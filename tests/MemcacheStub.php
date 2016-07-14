<?php

namespace Assertis\Util;

use Memcache;

/**
 * @author Mateusz Angulski <mateusz.angulski@assertis.co.uk>
 */
class MemcacheStub extends Memcache
{
    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var bool
     */
    private $areServersAdded = false;

    /**
     * @return MemcacheStub
     */
    public function withServersAdded()
    {
        $withServers = clone $this;
        $withServers->areServersAdded = true;

        return $withServers;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $var, $flag, $expire)
    {
        if (array_key_exists($key, $this->cache)) {
            return false;
        }
        $this->cache[$key] = $var;

        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, $timeout = 0)
    {
        if (!array_key_exists($key, $this->cache)) {
            return false;
        }
        unset($this->cache[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getversion()
    {
        return $this->areServersAdded;
    }
}
