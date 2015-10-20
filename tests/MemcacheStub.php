<?php

namespace Assertis\Util;

use Memcache;

class MemcacheStub extends Memcache
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $key
     * @param mixed $var
     * @param int $flag
     * @param int $expire
     *
     * @return array|bool
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
     * @param string $key
     * @param int $timeout
     *
     * @return bool
     */
    public function delete($key, $timeout = 0)
    {
        if (!array_key_exists($key, $this->cache)) {
            return false;
        }
        unset($this->cache[$key]);

        return true;
    }
}
