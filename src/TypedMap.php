<?php

namespace Assertis\Util;

use ArrayObject;
use InvalidArgumentException;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class TypedMap extends ArrayObject
{
    /**
     * @param array $input
     * @param int $flags
     * @param string $iterator_class
     */
    public function __construct(array $input = [], $flags = 0, $iterator_class = "ArrayIterator")
    {
        foreach ($input as $element) {
            if (!$this->accepts($element)) {
                throw new InvalidArgumentException($this->getErrorText());
            }
        }
        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    abstract public function accepts($value);

    /**
     * Set object to array
     *
     * @param mixed $index
     * @param mixed $newValue
     */
    public function offsetSet($index, $newValue)
    {
        if (!$this->accepts($newValue)) {
            throw new InvalidArgumentException($this->getErrorText());
        }
        parent::offsetSet($index, $newValue);
    }

    /**
     * Return error text
     *
     * @return string
     */
    protected function getErrorText()
    {
        return 'Bad type of value in ' . get_called_class();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($key, $default = false)
    {
        if ($this->has($key)) {
            return $this[$key];
        } elseif (func_num_args() > 1) {
            return $default;
        } else {
            throw new InvalidArgumentException("Invalid key: {$key}");
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this);
    }

    /**
     */
    public function clear()
    {
        $this->exchangeArray([]);
    }
}
