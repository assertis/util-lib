<?php

namespace Assertis\Util;

use ArrayObject;
use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class TypedMap extends ArrayObject implements JsonSerializable
{
    const DEFAULT_KEY_NAME = 'key';
    const DEFAULT_VALUE_NAME = 'value';

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
        parent::__construct([], $flags, $iterator_class);

        foreach ($input as $key => $value) {
            $this->assertValid($key, $value);

            $this->set($key, $value);
        }
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
            throw new InvalidArgumentException("Invalid key: " . var_export($key, true));
        }
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return parent::offsetGet($this->getKeyId($key));
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
     * Set object to array
     *
     * @param mixed $key
     * @param mixed $newValue
     */
    public function offsetSet($key, $newValue)
    {
        $this->assertValid($key, $newValue);

        $keyId = $this->getKeyId($key);
        $this->keys[$keyId] = $key;

        parent::offsetSet($keyId, $newValue);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($key)
    {
        $keyId = $this->getKeyId($key);

        unset($this->keys[$keyId]);
        parent::offsetUnset($keyId);
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
     * @return string
     */
    protected function getKeyId($key)
    {
        return is_object($key) ? spl_object_hash($key) : $key;
    }

    /**
     * @param mixed $keyId
     * @return mixed
     */
    protected function getKey($keyId)
    {
        return $this->keys[$keyId];
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return array_values($this->keys);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return array_values($this->getArrayCopy());
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($this->getKeyId($key), $this->keys);
    }

    /**
     */
    public function clear()
    {
        $this->keys = [];
        $this->exchangeArray([]);
    }

    /**
     * Serialize key.
     *
     * @param mixed $key
     * @return mixed
     * @throws Exception
     */
    protected function serializeKey($key)
    {
        return (is_object($key) && method_exists($key, 'toArray')) ? $key->toArray() : $key;
    }

    /**
     * Serialize value.
     *
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    protected function serializeValue($value)
    {
        return (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
    }

    /**
     * Turn this object into an array using toArray method on each element if they have it.
     *
     * @return array
     */
    public function toArray()
    {
        $keyName = static::getKeyName();
        $valueName = static::getValueName();

        $out = [];
        foreach ($this->getArrayCopy() as $keyId => $value) {
            $key = $this->getKey($keyId);
            $out[] = [
                $keyName => $this->serializeKey($key),
                $valueName => $this->serializeValue($value),
            ];
        }

        return $out;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Create a single key element from serialized data.
     *
     * @param mixed $data
     * @return mixed
     * @throws Exception
     */
    public static function deserializeKey($data)
    {
        throw new Exception(
            "To use fromArray deserialization feature please implement a static deserializeItem method."
        );
    }

    /**
     * Create a single value element from serialized data.
     *
     * @param mixed $data
     * @return mixed
     * @throws Exception
     */
    public static function deserializeValue($data)
    {
        throw new Exception(
            "To use fromArray deserialization feature please implement a static deserializeItem method."
        );
    }

    /**
     * @return string
     */
    public static function getKeyName()
    {
        return self::DEFAULT_KEY_NAME;
    }

    /**
     * @return string
     */
    public static function getValueName()
    {
        return self::DEFAULT_VALUE_NAME;
    }

    /**
     * @param array $data
     * @return static
     * @throws Exception
     */
    public static function fromArray(array $data)
    {
        $keyName = static::getKeyName();
        $valueName = static::getValueName();

        $out = new static();
        foreach ($data as $item) {
            $out->set(
                static::deserializeKey($item[$keyName]),
                static::deserializeValue($item[$valueName])
            );
        }

        return $out;
    }

    /**
     * @return TypedMapIterator
     */
    public function getIterator()
    {
        return new TypedMapIterator($this->keys, $this->getArrayCopy(), 0);
    }

    /**
     * @param callable $filter
     * @return static
     */
    public function filter(callable $filter)
    {
        $out = new static;
        
        foreach ($this as $key => $value) {
            if ($filter($key, $value)) {
                $out->set($key, $value);
            }
        }
        
        return $out;
    }
}
