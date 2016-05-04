<?php

namespace Assertis\Util;

use ArrayObject;
use Exception;
use InvalidArgumentException;
use Traversable;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 *
 * Note: an element matches a filter when $filter($element) === true.
 */
abstract class ObjectList extends ArrayObject
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

    // Methods that govern adding elements to the list

    /**
     * Return true if the value is acceptable for this list. Typically something like:
     *   return $value instanceof MyClass
     *
     * @param mixed $value
     * @return boolean
     */
    abstract public function accepts($value);

    /**
     * Check if {$newValue} is acceptable and set it.
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
     * Return error text for when accepts() returns false.
     *
     * @return string
     */
    protected function getErrorText()
    {
        return 'Bad type of value in ' . get_called_class();
    }

    //
    // Operations that modify this instance
    //

    /**
     * Add element to this list.
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
     * Add a list of elements to this list.
     *
     * @param Traversable $list
     * @return static
     */
    public function appendAll(Traversable $list)
    {
        foreach ($list as $element) {
            $this->append($element);
        }
        return $this;
    }

    /**
     * Remove an element from this list.
     *
     * @param mixed $element
     * @return self
     */
    public function delete($element)
    {
        $list = $this->getArrayCopy();
        $offset = array_search($element, $list, true);
        $this->offsetUnset($offset);

        return $this;
    }

    /**
     * Pop an element off the end of this list (see: array_pop).
     *
     * @return mixed
     */
    public function pop()
    {
        $list = $this->getArrayCopy();
        $element = array_pop($list);
        $this->exchangeArray($list);

        return $element;
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
        foreach ($this as $element) {
            if ($filter($element)) {
                $this->delete($element);

                return $element;
            }
        }

        return null;
    }

    /**
     * Sort this list using {$sorter} (see: usort).
     *
     * @param callable $sorter
     * @return static
     */
    public function sort(callable $sorter)
    {
        $list = $this->getArrayCopy();
        // Can't unit test anything using usort without suppressing its errors.
        @usort($list, $sorter);
        $this->exchangeArray($list);

        return $this;
    }

    //
    // Operations that return a new instance
    //

    /**
     * Return a new list containing only those elements matching {$filter}.
     *
     * @param callable $filter
     * @return static
     */
    public function filter(callable $filter)
    {
        return new static(array_filter($this->getArrayCopy(), $filter));
    }

    /**
     * Return a new list containing only those elements not present in {$otherList}.
     *
     * @param ObjectList $otherList
     * @return static
     */
    public function exclude(ObjectList $otherList)
    {
        return $this->filter(function ($element) use ($otherList) {
            return !$otherList->contains($element);
        });
    }

    /**
     * Return a new list containing {$length} elements starting on {$offset}.
     *
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function slice($offset, $length = null)
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length));
    }

    /**
     * Return a new list containing cloned copies of all elements from this list.
     *
     * @return static
     */
    public function getClone()
    {
        $out = new static;

        foreach ($this as $element) {
            $out->append(clone $element);
        }

        return $out;
    }

    //
    // Operations that return a single element
    //

    /**
     * Return first element.
     *
     * @return mixed
     */
    public function getFirst()
    {
        $copy = $this->getArrayCopy();

        return reset($copy);
    }

    /**
     * Return last element.
     *
     * @return mixed
     */
    public function getLast()
    {
        $copy = $this->getArrayCopy();

        return end($copy);
    }

    /**
     * Return first element matching {$filter}, or null.
     *
     * @param callable $filter
     * @return mixed|null
     */
    public function find(callable $filter)
    {
        foreach ($this as $element) {
            if ($filter($element)) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Return first element matching {$filter}, or throws a RuntimeException.
     *
     * @param callable $filter
     * @return mixed
     */
    public function get(callable $filter)
    {
        $element = $this->find($filter);

        if (null === $element) {
            throw new ObjectListElementNotFoundException(
                "Could not find an element in " . get_class($this) . " using a find filter."
            );
        }

        return $element;
    }

    //
    // Operations that return a list of this kind of list
    //

    /**
     * For each element create a key using {$grouper} and split this list into a series of lists based on that key.
     * The returned object is an ObjectListList which behaves like any other ObjectList.
     *
     * @param callable $grouper
     * @return ObjectListList
     */
    public function group(callable $grouper)
    {
        $out = [];

        foreach ($this as $element) {
            $value = $grouper($element);
            if (!array_key_exists($value, $out)) {
                $out[$value] = new static;
            }
            $list = &$out[$value];
            /** @var $list static */
            $list->append($element);
        }

        return new ObjectListList($out);
    }

    /**
     * Return all possible element order permutations this list. You can set the maximum number of returned
     * permutations.
     *
     * The returned object is an ObjectListList which behaves like any other ObjectList.
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

        return new ObjectListList($max ? array_slice($out, 0, $max) : $out);
    }

    /**
     * Create all permutations of an array of elements in the $out variable.
     *
     * @param array $out
     * @param array $list
     * @param array $perms
     * @param int $max
     */
    private function permute(&$out, $list, $perms = [], $max = null)
    {
        if ($max && count($out) >= $max) {
            return;
        } elseif (empty($list)) {
            $obj = clone $this;
            $obj->exchangeArray($perms);
            $out[] = $obj;
        } else {
            for ($i = count($list) - 1; $i >= 0; --$i) {
                $newList = $list;
                $newPermutations = $perms;
                list($element) = array_splice($newList, $i, 1);
                array_unshift($newPermutations, $element);
                $this->permute($out, $newList, $newPermutations, $max);
            }
        }
    }

    //
    // Serialization and deserialization
    //

    /**
     * Turn this object into an array using toArray method on each element if they have it.
     *
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
     * Create a single member element from serialized data.
     *
     * @param mixed $data
     * @return mixed
     * @throws Exception
     */
    public static function deserializeItem($data)
    {
        throw new Exception(
            "To use fromArray deserialization feature please implement a static deserializeItem method."
        );
    }

    /**
     * Create an object of current list type from a toArray serialized form.
     *
     * @param array $items
     * @return static
     * @throws Exception
     */
    public static function fromArray(array $items)
    {
        $out = [];
        foreach ($items as $item) {
            $out[] = static::deserializeItem($item);
        }

        return new static($out);
    }

    //
    // Operations that return a different representation of the list
    //

    /**
     * Applies {$mapper} to each element of this list and returns an array of outputs.
     *
     * @param callable $mapper
     * @return array
     */
    public function map(callable $mapper)
    {
        return array_map($mapper, $this->getArrayCopy());
    }

    /**
     * Iteratively reduce this list to a single value.
     *
     * @param callable $reducer
     * @param mixed|null $initial
     * @return mixed
     */
    public function reduce(callable $reducer, $initial = null)
    {
        return array_reduce($this->getArrayCopy(), $reducer, $initial);
    }

    /**
     * Iteratively reduce this list into a single value by adding up the result of {$valueProvider} for each element.
     *
     * @param callable $valueProvider
     * @param float|int|null $startValue
     * @return float|int
     */
    public function sum(callable $valueProvider, $startValue = null)
    {
        return $this->reduce(function ($total, $element) use ($valueProvider) {
            return $total + $valueProvider($element);
        }, $startValue);
    }

    /**
     * Return the number of elements matching {$filter}.
     *
     * @param callable $filter
     * @return int
     */
    public function countMatching(callable $filter)
    {
        $out = 0;
        foreach ($this as $element) {
            if ($filter($element)) {
                $out++;
            }
        }
        return $out;
    }

    /**
     * Return true if this list contains {$element}.
     *
     * @param mixed $element
     * @return bool
     */
    public function contains($element)
    {
        return false !== array_search($element, $this->getArrayCopy(), true);
    }

    //
    // Other
    //

    /**
     * Executes {$operation} for each of the elements and returns this list intact.
     *
     * @param callable $operation
     * @return static
     */
    public function each(callable $operation)
    {
        $this->map($operation);

        return $this;
    }
}
