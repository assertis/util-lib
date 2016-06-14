<?php

namespace Assertis\Util;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 *
 * @method static TypedMapAssociativeSerializationTrait deserializeKey(mixed $key)
 * @method static TypedMapAssociativeSerializationTrait deserializeValue(mixed $value)
 */
trait TypedMapAssociativeSerializationTrait
{
    /**
     * @param mixed $key
     * @return mixed
     */
    abstract public function serializeKey($key);

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract public function serializeValue($value);

    /**
     * @return array
     */
    abstract public function getArrayCopy();

    /**
     * @param mixed $keyId
     * @return mixed
     */
    abstract public function getKey($keyId);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    abstract public function set($key, $value);

    /**
     * @return array
     */
    public function toArray()
    {
        $out = [];
        foreach ($this->getArrayCopy() as $keyId => $value) {
            $key = $this->getKey($keyId);
            $out[$this->serializeKey($key)] = $this->serializeValue($value);
        }

        return $out;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        $out = new static();

        foreach ($data as $key => $value) {
            $out->set(
                $out::deserializeKey($key),
                $out::deserializeValue($value)
            );
        }

        return $out;
    }
}
