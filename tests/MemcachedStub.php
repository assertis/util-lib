<?php

namespace Assertis\Util;

use Memcached;

/**
 * @author Mateusz Angulski <mateusz.angulski@assertis.co.uk>
 */
class MemcachedStub extends Memcached
{
    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var bool
     */
    private $serversAdded = [];

    /**
     * @return MemcachedStub
     */
    public function withServersAdded()
    {
        $this->serversAdded = ['localhost:11211' => '1.2.6'];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $var, $expire = NULL)
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
        return $this->serversAdded;
    }
}
