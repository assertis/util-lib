<?php

namespace Assertis\Util\Stubs;

use Memcache;

/**
 * @author Mateusz Angulski <mateusz.angulski@assertis.co.uk>
 */
class MemcacheStub extends Memcache
{
    private array $cache = [];
    private bool $areServersAdded = false;

    public function withServersAdded(): MemcacheStub
    {
        $withServers = clone $this;
        $withServers->areServersAdded = true;

        return $withServers;
    }

    /**
     * @param string $key The key that will be associated with the item.
     * @param mixed $var The variable to store. Strings and integers are stored as is, other types are stored serialized.
     * @param int $flag [optional] <p>
     * Use <b>MEMCACHE_COMPRESSED</b> to store the item
     * compressed (uses zlib).
     * </p>
     * @param int $expire [optional] <p>Expiration time of the item.
     * If it's equal to zero, the item will never expire.
     * You can also use Unix timestamp or a number of seconds starting from current time, but in the latter case the number of seconds may not exceed 2592000 (30 days).</p>
     * @return bool Returns <b>TRUE</b> on success or <b>FALSE</b> on failure. Returns <b>FALSE</b> if such key already exist. For the rest Memcache::add() behaves similarly to Memcache::set().

     */
    public function add($key, $value, $flags = null, $exptime = null, $cas = null): bool
    {
        if (array_key_exists($key, $this->cache)) {
            return false;
        }

        $this->cache[$key] = $value;

        return true;
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
    public function getVersion(): bool
    {
        return $this->areServersAdded;
    }
}
