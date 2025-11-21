<?php

declare(strict_types=1);

namespace Assertis\Util;

use ArrayObject;
use InvalidArgumentException;
use JsonSerializable;
use LogicException;
use Traversable;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 *
 * Note: an element matches a filter when $filter($element) === true.
 *
 * @template T of object
 * @extends ArrayObject<mixed, T>
 */
abstract class ObjectList extends ArrayObject implements JsonSerializable
{
	/**
	 * @param T[] $input
	 * @param int $flags
	 * @param class-string $iteratorClass
	 */
    public function __construct(array $input = [], int $flags = 0, string $iteratorClass = 'ArrayIterator')
    {
        foreach ($input as $element) {
            if (!$this->accepts($element)) {
                throw new InvalidArgumentException($this->getErrorText());
            }
        }
        parent::__construct($input, $flags, $iteratorClass);
    }

    // Methods that govern adding elements to the list

    /**
     * Return true if the value is acceptable for this list. Typically something like:
     *   return $value instanceof MyClass
     */
    abstract public function accepts(mixed $value): bool;

	/**
	 * Check if {$newValue} is acceptable and set it.
	 * @param mixed $key
	 * @param T $value
	 */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (!$this->accepts($value)) {
            throw new InvalidArgumentException($this->getErrorText());
        }

        parent::offsetSet($key, $value);
    }

    /**
     * Return error text for when accepts() returns false.
     */
    protected function getErrorText(): string
    {
        return 'Bad type of value in ' . static::class;
    }

    //
    // Operations that modify this instance
    //

    /**
     * Add element to this list.
	 * @param T $value
     */
    public function append(mixed $value): void
    {
        if (!$this->accepts($value)) {
            throw new InvalidArgumentException($this->getErrorText());
        }

        parent::append($value);
    }

    /**
     * Add a list of elements to this list.
	 * @param Traversable<mixed, T> $list
     */
    public function appendAll(Traversable $list): static
    {
        foreach (iterator_to_array($list) as $element) {
            $this->append($element);
        }

        return $this;
    }

    /**
     * Remove an element from this list.
	 * @param T $element
     */
    public function delete(mixed $element): static
    {
        $list = $this->getArrayCopy();
        $offset = array_search($element, $list, true);
        $this->offsetUnset($offset);

        return $this;
    }

    /**
     * Pop an element off the end of this list (see: array_pop).
	 * @return T|null
     */
    public function pop(): mixed
    {
        $list = $this->getArrayCopy();
        $element = array_pop($list);
        $this->exchangeArray($list);

        return $element;
    }

    /**
     * Remove from list and return the first list value that matches callable filter.
     * Technically it's `unshift`, not `pop`, because it returns the first, not the last element.
	 * @param callable(T): bool $filter
	 * @return T|null
	 */
    public function popMatching(callable $filter): mixed
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
	 * @param callable(T, T): int $sorter
	 */
    public function sort(callable $sorter): static
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
	 * @param callable(T): bool $filter
	 */
    public function filter(callable $filter): static
    {
        return new static(array_filter($this->getArrayCopy(), $filter));
    }

    /**
     * Return a new list containing only those elements not present in {$otherList}.
	 * @param ObjectList<T> $otherList
	 */
    public function exclude(ObjectList $otherList): static
    {
        return $this->filter(function ($element) use ($otherList) {
            return !$otherList->contains($element);
        });
    }

    /**
     * Return a new list containing only those elements present in {$otherList}.
	 * @param ObjectList<T> $otherList
	 */
    public function intersect(ObjectList $otherList): static
    {
        return $this->filter(function ($element) use ($otherList) {
            return $otherList->contains($element);
        });
    }

    /**
     * Returns a new list containing one element for each {$selector($element)} value (i.e. array_unique using
     * a callback for each value).
	 * @param callable(T): mixed $selector
	 */
    public function uniqueFor(callable $selector): static
    {
        $keys = [];

        return $this->filter(function ($item) use ($selector, &$keys) {
            $value = $selector($item);
            $key = is_object($value) ? spl_object_hash($value) : $value;

            if (array_key_exists($key, $keys)) {
                return false;
            }

            $keys[$key] = true;

            return true;
        });
    }

    /**
     * Return a new list containing {$length} elements starting on {$offset}.
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length));
    }

    /**
     * Return a new list containing cloned copies of all elements from this list.
     */
    public function getClone(): static
    {
        $out = new static();

        foreach ($this as $element) {
            $out->append(clone $element);
        }

        return $out;
    }

    //
    // Operations that return a single element
    //

    /**
     * Return the first element.
	 * @return T|null
     */
    public function getFirst(): mixed
    {
        $copy = $this->getArrayCopy();

        return reset($copy);
    }

    /**
     * Return the last element.
	 * @return T|null
	 */
    public function getLast(): mixed
    {
        $copy = $this->getArrayCopy();

        return end($copy);
    }

    /**
     * Return first element matching {$filter}, or null.
	 * @return T|null
	 */
    public function find(callable $filter): mixed
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
	 * @param callable(T): bool $filter
	 * @return T
	 * @throws ObjectListElementNotFoundException
     */
    public function get(callable $filter): mixed
    {
        $element = $this->find($filter);

        if ($element === null) {
            throw new ObjectListElementNotFoundException(
                sprintf(
                    'Could not find an element in %s using a find filter.',
                    get_class($this)
                )
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
     * @param callable(T): mixed $grouper
     * @return ObjectListList<T>
     */
    public function group(callable $grouper): ObjectListList
    {
        $out = [];

        foreach ($this as $element) {
            $value = $grouper($element);
            if (!array_key_exists($value, $out)) {
                $out[$value] = new static();
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
	 * @return ObjectListList<T>
     */
    public function getAllPermutations(?int $max = null): ObjectListList
    {
        if (count($this) === 0) {
            return new ObjectListList();
        }

        $out = [];
        $this->permute($out, $this->getArrayCopy(), [], $max);

        return new ObjectListList($max ? array_slice($out, 0, $max) : $out);
    }

    /**
     * Create all permutations of an array of elements in the $out variable.
     */
    private function permute(array &$out, array $list, array $perms = [], ?int $max = null): void
    {
        if ($max && count($out) >= $max) {
            return;
        }

        if (empty($list)) {
            $obj = clone $this;
            $obj->exchangeArray($perms);
            $out[] = $obj;
        } else {
            for ($i = count($list) - 1; $i >= 0; --$i) {
                $newList = $list;
                $newPermutations = $perms;
                [$element] = array_splice($newList, $i, 1);
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
     */
    public function toArray(): array
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create a single member element from serialized data.
     *
	 * @return T
     * @throws LogicException
     */
    public static function deserializeItem(mixed $data): mixed
    {
        throw new LogicException(
            sprintf(
                'To use fromArray deserialization feature please implement a static deserializeItem method in %s.',
                static::class
            )
        );
    }

    /**
     * Create an object of current list type from a toArray serialized form.
	 * @return ObjectList<T>
     */
    public static function fromArray(array $items): static
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
	 * @template U
	 * @param callable(T): U $mapper
	 * @return U[]
     */
    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->getArrayCopy());
    }

    /**
     * Iteratively reduce this list to a single value.
	 * @template U
	 * @param callable(U, T): U $reducer
	 * @param U|null $initial
	 * @return U|null
	 */
    public function reduce(callable $reducer, mixed $initial = null): mixed
    {
        return array_reduce($this->getArrayCopy(), $reducer, $initial);
    }

	/**
	 * Iteratively reduce this list into a single value by adding up the result of {$valueProvider} for each element.
	 *
	 * @template U of float|int
	 * @param callable(T): U $valueProvider
	 * @param U|null $startValue
	 * @return U|null
	 */
    public function sum(callable $valueProvider, mixed $startValue = null): int|float
    {
        return $this->reduce(function ($total, $element) use ($valueProvider) {
            return $total + $valueProvider($element);
        }, $startValue);
    }

    /**
     * Return the number of elements matching {$filter}.
	 *
	 * @param callable(T): bool $filter
	 */
    public function countMatching(callable $filter): int
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
     * Whether this list contains any elements matching {$filter}.
	 *
	 * @param callable(T): bool $filter
	 */
    public function hasMatching(callable $filter): bool
    {
        foreach ($this as $element) {
            if ($filter($element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if this list contains {$element}.
	 *
	 * @param T $element
     */
    public function contains(mixed $element): bool
    {
        return in_array($element, $this->getArrayCopy(), true);
    }

    //
    // Other
    //

    /**
     * Executes {$operation} for each of the elements and returns this list intact.
	 *
	 * @param callable(T): void $operation
	 */
    public function each(callable $operation): static
    {
        $this->map($operation);

        return $this;
    }
}
