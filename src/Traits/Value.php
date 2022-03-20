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
        $this->initialize();
        $this->setPropertiesValue($attributes);
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

    public static function hiddenProperty()
    {
        return '___hidden';
    }

    public static function guardedProperty()
    {
        return '___guarded';
    }

    final protected function getRawAttribute(string $name)
    {
        [$properties, $attributes] = [$this->getProperties() ?? [], $this->getRawAttributes()];
        if (!$this->__ASSOCIATIVE__) {
            return $attributes[$name];
        }
        if (null !== ($value = $attributes[$name] ?? null)) {
            return $value;
        }
        // if (\array_key_exists($name, $properties)) {
        //     return $this->callPropertyGetter($name, $value);
        // }
        $key = Arr::search($name, $properties);
        if ($key && ($value = $attributes[$key])) {
            return $value;
        }
        return null;
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
