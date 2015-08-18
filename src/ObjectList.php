<?php

namespace Assertis\Util;

use ArrayObject;
use InvalidArgumentException;
use Traversable;

/**
 * Class ObjectList
 * @package Assertis\Util
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 *
 * Class contain logic for object iterator
 */
abstract class ObjectList extends ArrayObject
{

    /**
     * @param mixed $value
     * @return boolean
     */
    abstract public function accepts($value);

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
     * @param callable $filter
     * @return int
     */
    public function countMatching(callable $filter)
    {
        $out = 0;
        foreach ($this as $item) {
            if ($filter($item)) {
                $out++;
            }
        }
        return $out;
    }

    /**
     * @param callable $filter
     * @return static
     */
    public function filter(callable $filter)
    {
        return new static(array_filter($this->getArrayCopy(), $filter));
    }

    /**
     * @param callable $mapper
     * @return array
     */
    public function map(callable $mapper)
    {
        return array_map($mapper, $this->getArrayCopy());
    }

    /**
     * @param callable $operation
     * @return static
     */
    public function each(callable $operation)
    {
        $this->map($operation);
        return $this;
    }

    /**
     * @param callable $reducer
     * @param mixed|null $initial
     * @return mixed
     */
    public function reduce(callable $reducer, $initial = null)
    {
        return array_reduce($this->getArrayCopy(), $reducer, $initial);
    }

    /**
     * @param callable $grouper
     * @return static[]
     */
    public function group(callable $grouper)
    {
        $out = [];

        foreach ($this as $item) {
            $value = $grouper($item);
            if (!array_key_exists($value, $out)) {
                $out[$value] = new static;
            }
            $list = &$out[$value];
            /** @var $list static */
            $list->append($item);
        }

        return $out;
    }

    /**
     * Append object to array
     *
     * @param mixed $value
     * @return self
     */
    public function append($value)
    {
        if (!$this->accepts($value)) {
            throw new InvalidArgumentException($this->getErrorText());
        }
        parent::append($value);

        return $this;
    }

    /**
     * Append a list of objects to this list
     *
     * @param Traversable $list
     *
     * @return static
     */
    public function appendAll(Traversable $list)
    {
        foreach ($list as $item) {
            $this->append($item);
        }
        return $this;
    }

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
     * Create all permutations of the list.
     *
     * @param int $max
     * @return static[]
     */
    public function getAllPermutations($max = null)
    {
        $out = [];
        if (count($this) === 0) {
            return $out;
        }
        $this->permute($out, $this->getArrayCopy(), [], $max);
        return $max ? array_slice($out, 0, $max) : $out;
    }

    /**
     * Create all permutations of an array of items in the $out variable.
     *
     * @param array $out
     * @param array $items
     * @param array $perms
     * @param int $max
     */
    private function permute(&$out, $items, $perms = [], $max = null)
    {
        if ($max && count($out) >= $max) {
            return;
        } elseif (empty($items)) {
            $obj = clone $this;
            $obj->exchangeArray($perms);
            $out[] = $obj;
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newItems = $items;
                $newPermutations = $perms;
                list($item) = array_splice($newItems, $i, 1);
                array_unshift($newPermutations, $item);
                $this->permute($out, $newItems, $newPermutations, $max);
            }
        }
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        $items = $this->getArrayCopy();
        $item = array_pop($items);
        $this->exchangeArray($items);
        return $item;
    }

    /**
     * @param mixed $item
     * @return self
     */
    public function delete($item)
    {
        $items = $this->getArrayCopy();
        $offset = array_search($item, $items, true);
        $this->offsetUnset($offset);
        return $this;
    }

    /**
     * Remove from list and return the first list value that matches callable filter.
     * Technically it's `unshift`, not `pop`, because it returns the first, not the last element.
     *
     * @param callable $filter
     * @return mixed|null
     */
    public function popMatching(callable $filter)
    {
        foreach ($this as $item) {
            if ($filter($item)) {
                $this->delete($item);
                return $item;
            }
        }
        return null;
    }

    /**
     * Return first element
     *
     * @return mixed
     */
    public function getFirst()
    {
        $copy = $this->getArrayCopy();

        return reset($copy);
    }

    /**
     * Return last element
     *
     * @return mixed
     */
    public function getLast()
    {
        $copy = $this->getArrayCopy();

        return end($copy);
    }

    /**
     * @return static
     */
    public function getClone()
    {
        $out = new static;
        foreach ($this as $item) {
            $out->append(clone $item);
        }
        return $out;
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function contains($item)
    {
        return false !== array_search($item, $this->getArrayCopy(), true);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $out = [];
        foreach ($this->getArrayCopy() as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $out[$key] = $value->toArray();
            } else {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    /**
     * @param ObjectList $otherList
     * @return static
     */
    public function exclude(ObjectList $otherList)
    {
        return $this->filter(function ($item) use ($otherList) {
            return !$otherList->contains($item);
        });
    }

    /**
     * @param callable $sorter
     * @return static
     */
    public function sort(callable $sorter)
    {
        $items = $this->getArrayCopy();
        // Can't unit test anything using usort without suppressing its errors.
        @usort($items, $sorter);
        $this->exchangeArray($items);
        return $this;
    }
}
