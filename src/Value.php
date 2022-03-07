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

namespace Drewlabs\Immutable;

use Drewlabs\Contracts\Clonable;
use Drewlabs\Contracts\Support\Immutable\ValueObjectInterface;
use Drewlabs\Immutable\Traits\ValueObject;

abstract class Value implements ValueObjectInterface, Clonable, \IteratorAggregate
{
    use ValueObject;

    /**
     * Creates an instance of Drewlabs\Immutable\ValueObject::class.
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
        foreach ($this->getAttributes() as $key => $value) {
            yield $key => $value;
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

    /**
     * Overridable method returning the list of serializable properties
     *
     * @return array
     */
    protected function getJsonableAttributes()
    {
        if ($this->___properties) {
            return $this->___properties;
        }
        return [];
    }

    /**
     * @return Accessible|mixed
     */
    final protected function getAttributes()
    {
        return $this->___attributes;
    }
}
