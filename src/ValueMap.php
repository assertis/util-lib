<?php

namespace Assertis\Util;

use Exception;
use InvalidArgumentException;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ValueMap
{

    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param mixed $value
     */
    public function add($value): void
    {
        $this->data[$value] = true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(mixed $key, mixed $default = false): mixed
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        if (func_num_args() > 1) {
            return $default;
        }

        throw new InvalidArgumentException("Invalid key: {$key}");
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     */
    public function clear(): void
    {
        $this->data = [];
    }
}
