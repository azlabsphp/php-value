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
 * @method void __set(string $name, $value)
 * @method mixed __get(string $name)
 * 
 * @package Drewlabs\PHPValue
 */
class ObjectProxy implements Adaptable, \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var object
     */
    private $proxied;

    /**
     * Creates an object proxy instant that provides
     * 
     * @param object $object 
     * @return void 
     */
    public function __construct(object $object)
    {
        $this->proxied = $object;
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
                $property = trim($keys[$index]);
                $output = !$is_object ? $output[$property] ?? null : $output->{$property} ?? null;
                ++$index;
            }

            return $output;
        }
        return $this->offsetGet($name);
    }

    public function propertyExists(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function setPropertyValue(string $name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Merged attributes into the proxied object
     * 
     * @param array<string,mixed> $attributes
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function merge(array $attributes = [])
    {
        $self = $this;

        foreach ($attributes as $key => $value) {
            $self->offsetSet($key, $value);
        }

        return $self;
    }

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    public function offsetExists($name): bool
    {
        return property_exists($this->proxied, $name);
    }

    public function offsetGet($name)
    {
        return property_exists($this->proxied, $name) ? $this->proxied->{$name} : null;
    }

    public function offsetSet($name, $value): void
    {
        $this->proxied->{$name} = $value;
    }

    public function offsetUnset($name): void
    {
        unset($this->proxied->{$name});
    }

    public function getIterator(): \Traversable
    {
        foreach (get_object_vars($this->proxied) as $key => $value) {
            yield $key => $value;
        }
    }

    public function __get(string $name)
    {
        return $this->proxied->offsetGet($name);
    }

    public function __set(string $name, $value)
    {
        $this->proxied->offsetSet($name, $value);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->proxied, $name], $arguments);
    }

    public function __isset(string $name)
    {
        return isset($this->proxied->{$name});
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function __clone()
    {
        $this->proxied = clone $this->proxied;
    }
    // #region String __repr__
    public function __toString()
    {
        return json_encode($this->toArray(), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }
    // #endregion Macros
}
