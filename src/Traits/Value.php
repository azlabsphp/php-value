<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Arr;
use ReturnTypeWillChange;

trait Value
{
    use BaseTrait, Castable;

    /**
     * Creates an instance of Drewlabs\PHPValue\ValueObject::class.
     *
     * @param array|object $attributes
     */
    public function __construct($attributes = [])
    {
        $this->initializeAttributes();
        if (\is_array($attributes)) {
            $this->setAttributes($attributes);
        } elseif (\is_object($attributes) || ($attributes instanceof \stdClass)) {
            $this->fromStdClass($attributes);
        }
    }

    /**
     * Provides an object oriented iterator over the this object keys and values.
     *
     * @return \Traversable
     */
    public function each(\Closure $callback)
    {
        return $this->getAttributes()->each($callback);
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        [$fillables, $hidden] = [$this->loadBindings(), $this->getHidden()];
        if ($this->___associative) {
            foreach ($fillables as $key => $value) {
                if (!\in_array($key, $hidden, true)) {
                    yield $value => $this->callPropertyGetter($key, $this->getRawAttribute($key));
                }
            }
        } else {
            foreach ($fillables as $key) {
                if (!\in_array($key, $hidden, true)) {
                    yield $key => $this->callPropertyGetter($key, $this->getRawAttribute($key));
                }
            }
        }
    }

    public static function hiddenProperty()
    {
        return '___hidden';
    }

    public static function guardedProperty()
    {
        return '___guarded';
    }

    final protected function getAttributes()
    {
        return $this->___attributes;
    }

    final protected function getRawAttribute(string $name)
    {
        $fillables = $this->loadBindings() ?? [];
        if (!$this->___associative) {
            return $this->___attributes[$name];
        }
        if (null !== ($value = $this->___attributes[$name] ?? null)) {
            return $value;
        }
        // if (\array_key_exists($name, $fillables)) {
        //     return $this->callPropertyGetter($name, $value);
        // }
        $key = Arr::search($name, $fillables);
        if ($key && ($value = $this->___attributes[$key])) {
            return $value;
        }
        return null;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }
}
