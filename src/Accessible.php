<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue;

use Drewlabs\PHPValue\Contracts\Adaptable;

/**
 * PHP stdClass extension usable as array accessible object.
 *
 * ```php
 * $object = new Accessible;
 *
 * // Checking if a property is set on the object
 * $isset = isset($object)
 *
 * // Accessing property of the object
 * $value = $object['property'];
 *
 * // Setting property
 * $object['property'] = $value;
 *
 * // Checking if object has property
 * $object->hasProperty('property');
 *
 * // Check if object is empty
 * $isEmpty = $object->isEmpty();
 *
 * // Looping through object keys
 * $object->each(function($key, $value) {
 *        // Perform action on key and value
 * });
 *
 * // Converting object to array
 * $array = $object->toArray();
 * ```
 */
class Accessible implements \ArrayAccess, Adaptable, \IteratorAggregate, \Countable
{
    /**
     * Minimum capacity.
     *
     * @var int
     */
    public const MIN_CAPACITY = 8;

    /**
     * @var int internal capacity
     */
    private $capacity = self::MIN_CAPACITY;

    /**
     * Internal map data structure.
     *
     * @var array<Pair>
     */
    private $__PAIRS__ = [];

    public function __isset(string $name)
    {
        return null !== $this->lookupKey($name);
    }

    // TODO : review the object formatting
    public function __toString()
    {
        return json_encode(iterator_to_array($this->getIterator()), \JSON_PRETTY_PRINT);
    }

    public function __clone()
    {
        $pairs = [];
        foreach ($this->__PAIRS__ as $key => $value) {
            $pairs[$key] = \is_object($value) ? clone $value : $value;
        }
        $this->__PAIRS__ = $pairs;
    }

    public function getPropertyValue(string $name)
    {
        if (false !== strpos($name, '.')) {
            $keys = explode('.', $name);
            $last = \count($keys);
            $index = 1;
            $output = $this->offsetGet(trim($keys[0]));
            while ($index < $last) {
                if (!(($is_object = \is_object($output)) || \is_array($output))) {
                    return null;
                }
                $prop = trim($keys[$index]);
                $output = !$is_object ? $output[$prop] : $output->{$prop};
                ++$index;
            }

            return $output;
        }

        return $this->offsetGet($name);
    }

    public function setPropertyValue(string $name, $value)
    {
        $this->offsetSet($name, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return null !== $this->lookupKey($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (($pair = $this->lookupKey($offset))) {
            return $pair->value;
        }

        return null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        $pair = $this->lookupKey($offset);

        if ($pair) {
            $pair->value = $value;
        } else {
            $this->checkCapacity();
            $this->__PAIRS__[] = new Pair($offset, $value);
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        foreach ($this->__PAIRS__ as $position => $pair) {
            if ($pair->key === $offset) {
                $pair = $this->__PAIRS__[$position];
                array_splice($this->__PAIRS__, $position, 1, null);
                $this->checkCapacity();

                return;
            }
        }
    }

    /**
     * Object oriented implementation of the property exists
     * function on this object.
     *
     * @param mixed $prop
     */
    public function propertyExists($prop): bool
    {
        return $this->offsetExists($prop);
    }

    public function isEmpty()
    {
        return 0 === \count($this);
    }

    /**
     * Provides an enumerator on the object properties key and value.
     * It provides an object oriented way of looping through object
     * keys and values.
     *
     * Can be used instead of:
     * ```php
     * $p = new stdClass;
     * foreach ($p as $key => $value ) {
     *  //... Provides implementation details
     * }
     * ```
     *
     * @return \Generator<int, mixed, mixed, void>
     */
    public function each(\Closure $callback)
    {
        foreach ($this->getIterator() as $key => $value) {
            yield $callback(...[$key, $value]);
        }
    }

    /**
     * Provides an enumerator on the object properties key and value.
     *
     * Can be used instead of:
     * ```php
     * $p = new stdClass;
     * foreach ($p as $key => $value ) {
     *  //... Provides implementation details
     * }
     * ```
     *
     * @return \Generator<int, mixed, mixed, void>
     */
    public function forEach(\Closure $callback)
    {
        return $this->each($callback);
    }

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * @param array<string,mixed> $attributes
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    public function merge(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        foreach ($this->__PAIRS__ as $pair) {
            yield $pair->key => $pair->value;
        }
    }

    // #region \Ds Namespace
    /**
     * Returns the current capacity.
     */
    public function capacity(): int
    {
        return $this->capacity;
    }

    /**
     * Returns the total element in the map.
     *
     * @return int<0, \max>
     */
    public function count(): int
    {
        return \count($this->__PAIRS__);
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $key
     *
     * @return Pair|null
     *
     * @psalm-return Pair<TKey, TValue>|null
     */
    private function lookupKey($key)
    {
        foreach ($this->__PAIRS__ as $pair) {
            if ($pair->key === $key) {
                return $pair;
            }
        }
    }

    /**
     * @return float the structures growth factor
     */
    private function getGrowthFactor(): float
    {
        return 2;
    }

    /**
     * @return float to multiply by when decreasing capacity
     */
    private function getDecayFactor(): float
    {
        return 0.5;
    }

    /**
     * @return float the ratio between size and capacity when capacity should be
     *               decreased
     */
    private function getTruncateThreshold(): float
    {
        return 0.25;
    }

    /**
     * Checks and adjusts capacity if required.
     */
    private function checkCapacity()
    {
        if ($this->shouldIncreaseCapacity()) {
            $this->increaseCapacity();
        } else {
            if ($this->shouldDecreaseCapacity()) {
                $this->decreaseCapacity();
            }
        }
    }

    /**
     * @return bool whether capacity should be increased
     */
    private function shouldIncreaseCapacity(): bool
    {
        return $this->count() >= $this->capacity();
    }

    private function nextCapacity(): int
    {
        return (int) ($this->capacity() * $this->getGrowthFactor());
    }

    /**
     * Called when capacity should be increased to accommodate new values.
     */
    private function increaseCapacity()
    {
        $this->capacity = max($this->count(), $this->nextCapacity());
    }

    /**
     * Called when capacity should be decrease if it drops below a threshold.
     */
    private function decreaseCapacity()
    {
        $this->capacity = max(self::MIN_CAPACITY, (int) ($this->capacity() * $this->getDecayFactor()));
    }

    /**
     * @return bool whether capacity should be increased
     */
    private function shouldDecreaseCapacity(): bool
    {
        return \count($this) <= $this->capacity() * $this->getTruncateThreshold();
    }
}
