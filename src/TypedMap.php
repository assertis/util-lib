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
     * @var array
     */
    private $keys = [];

    /**
     * @param array $input
     * @param int $flags
     * @param string $iterator_class
     */
    public function __construct(array $input = [], $flags = 0, $iterator_class = "ArrayIterator")
    {
        foreach ($input as $key => $value) {
            $this->assertValid($key, $value);
        }

        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    abstract public function accepts($value);

    /**
     * @param mixed $key
     * @return bool
     */
    public function acceptsKey($key)
    {
        return true;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    private function assertValid($key, $value)
    {
        if (!$this->acceptsKey($key)) {
            throw new InvalidArgumentException($this->getKeyErrorText());
        }

        if (!$this->accepts($value)) {
            throw new InvalidArgumentException($this->getValueErrorText());
        }
    }

    /**
     * Set object to array
     *
     * @param mixed $key
     * @param mixed $newValue
     */
    public function offsetSet($key, $newValue)
    {
        $this->assertValid($key, $newValue);

        $keyId = is_object($key) ? spl_object_hash($key) : $key;
        $this->keys[$keyId] = $key;

        parent::offsetSet($keyId, $newValue);
    }

    /**
     * @deprecated
     * @return string
     */
    protected function getErrorText()
    {
        return 'Bad type of value in ' . get_called_class();
    }

    /**
     * Return error text
     *
     * @return string
     */
    protected function getValueErrorText()
    {
        return $this->getErrorText();
    }

    /**
     * Return error text
     *
     * @return string
     */
    protected function getKeyErrorText()
    {
        return 'Bad type of key in ' . get_called_class();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        $keyId = is_object($key) ? spl_object_hash($key) : $key;

        return parent::offsetGet($keyId);
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
            throw new InvalidArgumentException("Invalid key: ".var_export($key, true));
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return is_object($key) ?
            in_array($key, $this->keys) :
            array_key_exists($key, $this);
    }

    /**
     */
    public function clear()
    {
        $this->exchangeArray([]);
    }
}
