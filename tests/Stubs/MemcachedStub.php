<?php

namespace Assertis\Util\Stubs;

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
    public function withServersAdded(): static
    {
        $this->serversAdded = ['localhost:11211' => '1.2.6'];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $expiration = 0, $udf_flags = 0): bool|array
    {
        if (array_key_exists($key, $this->cache)) {
            return false;
        }
        $this->cache[$key] = $value;

        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, $timeout = 0): bool
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
    public function getversion(): bool|array
    {
        return $this->serversAdded;
    }
}
