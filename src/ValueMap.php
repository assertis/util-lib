<?php

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */

namespace Assertis\Util;

use Exception;

class ValueMap {

    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * @param mixed $value
     */
    public function add($value) {
        $this->data[$value] = true;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     * @throws Exception
     */
    public function get($key, $default=false) {
        if ($this->has($key)) {
            return $this->data[$key];
        }
        elseif (func_num_args() > 1) {
            return $default;
        }
        else {
            throw new Exception("Invalid key: {$key}");
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key) {
        return array_key_exists($key, $this->data);
    }

    /**
     */
    public function clear() {
        $this->data = [];
    }

}
