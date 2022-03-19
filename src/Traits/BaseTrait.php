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

use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Accessible;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Exceptions\ImmutableValueException;

trait BaseTrait
{
    use ArrayAccess, Clonable;

    /**
     * Properties container.
     *
     * @var object
     */
    protected $___attributes;

    /**
     * Indicated whether the bindings should load guarded properties.
     *
     * @var bool
     */
    private $___loadguards = false;

    /**
     * @var bool
     */
    private $___associative = false;

    /**
     * Makes class attributes accessible through -> syntax.
     *
     * @param  $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->callPropertyGetter($name, $this->getRawAttribute($name));
    }

    /**
     * Makes sure the object properties are not set by external code making the object immutable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws ImmutableValueException
     */
    public function __set($name, $value)
    {
        if (in_array($name, ['___hidden'])) {
            return $this->$name = $value;
        }
        throw new ImmutableValueException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->___attributes->__toString();
    }

    /**
     * 
     * @description Creates a copy of the current object changing the changing old attributes
     * values with newly proivided ones
     */
    public function copyWith(array $attributes, $setGuarded = false)
    {
        // Clone the current object to make default copy of it
        return $this->clone()->setAttributes($attributes, $setGuarded);
    }

    /**
     * @description Create an instance of class from a standard PHP class
     */
    public function fromStdClass($object_)
    {
        $fillables = $this->loadBindings();
        if ($this->___associative) {
            foreach ($fillables as $key => $value) {
                if (property_exists($object_, $value) && $this->isNotGuarded($value, true)) {
                    $this->setAttribute($key, $object_->{$value});
                }
            }
        } else {
            foreach ($fillables as $key) {
                if (property_exists($object_, $key) && $this->isNotGuarded($key, true)) {
                    $this->setAttribute($key, $object_->{$key});
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray()
    {
        return iterator_to_array((function () {
            foreach ($this->___attributes as $key => $value) {
                if (!\in_array($key, $this->getHidden(), true)) {
                    yield $key => $value;
                }
            }
        })());
    }

    /**
     * [[loadGuardedAttributes]] property getter.
     *
     * @return bool
     */
    public function getLoadGuardedAttributes()
    {
        return $this->___loadguards;
    }

    //region Array access method definitions

    /**
     * Query for the provided $key in the object attribute.
     *
     * @param \Closure|mixed|null $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        // TODO : Call value getter and pass it to the propertyGetter method
        return $this->callPropertyGetter(
            $key,
            $this->getRawAttribute($key),
            is_callable($default) ? $default : function () use ($key, $default) {
                $result = drewlabs_core_array_get(
                    $this->___attributes ? $this->___attributes->toArray() : [],
                    $key,
                    function () use ($key) {
                        return $this->__get($key);
                    }
                );
                return $result ?? $default;
            }
        );
    }

    public function setHidden(array $value)
    {
        $this->___hidden = $value;
        return $this;
    }

    public function getHidden()
    {
        return $this->___hidden ?? [];
    }

    /**
     * Merge hidden property values.
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->___hidden = array_merge($this->getHidden() ?: [], $value ?: []);

        return $this;
    }

    /**
     * Merge object attributes.
     * 
     * @param array|\stdClass $attributes 
     * @return BaseTrait
     */
    public function merge($attributes = [])
    {
        $attributes = $attributes instanceof ArrayableInterface ?
            $attributes->toArray() : ($attributes ? (array)$attributes : []);
        return $this->copyWith($attributes);
    }

    /**
     * Copy object properties changing existing property values with
     * user provided ones.
     * 
     * @param array|\stdClass $attributes 
     * @return self 
     */
    public function copy($attributes = [])
    {
        $attributes = $attributes instanceof ArrayableInterface ?
            $attributes->toArray() : ($attributes ? (array)$attributes : []);
        return $this->copyWith($attributes);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function callPropertySetter($name, $value)
    {
        $method = 'set' . Str::camelize($name) . 'Attribute';
        $result = $this->{$method}($value);
        if (null !== $result) {
            $this->___attributes[$name] = $result;
        }
        return $this;
    }

    protected function callPropertyGetter($name, $value, \Closure $default = null)
    {
        if ($this->hasPropertyGetter($name)) {
            $method = 'get' . Str::camelize($name) . 'Attribute';
            return $this->{$method}($value);
        }
        $default = function () use ($value, $default) {
            if (null === ($value = $value)) {
                return $default ? $default() : $value;
            }
            // Returns null if no matching found
            return $value;
        };
        // If the current object is instance of {@see CastsAware} and interface
        // exist {@see CastAware} we call the getCastableProperty method
        if (
            (interface_exists(CastsAware::class)) &&
            ($this instanceof CastsAware) &&
            (null !== ($this->getCasts()[$name] ?? null))
        ) {
            return $this->getCastableProperty($name, $value, $default);
        }
        return $default();
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNotGuarded($value, bool $load = false)
    {
        return $load ? true : !\in_array($value, $this->___guarded ?? [], true);
    }

    /**
     * Overridable method returning the list of serializable properties
     *
     * @return array
     */
    protected function getJsonableAttributes()
    {
        if (property_exists($this, '___properties')) {
            return $this->___properties ?? [];
        }
        return [];
    }

    /**
     * @return self
     */
    protected function initializeAttributes()
    {
        $this->___attributes = new Accessible;
        $this->___associative = Arr::isallassoc($this->getJsonableAttributes());
        return $this;
    }

    /**
     * @return array
     */
    final public function getRawAttributes()
    {
        return $this->___attributes->toArray();
    }

    final protected function mergeRawAttributes(array $attributes = [])
    {
        $this->___attributes->merge($attributes);
        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    final protected function setRawAttribute(string $name, $value)
    {
        $this->___attributes[$name] = $value;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    final protected function setRawAttributes($attributes)
    {
        $this->___attributes = $attributes ?? clone $this->___attributes ?? new Accessible;
        return $this;
    }

    /**
     * Attributes setter internal method.
     *
     * @param bool $setGuarded
     *
     * @return self
     */
    protected function setAttributes(array $attributes, $setGuarded = false)
    {
        $this->___loadguards = $setGuarded;
        $fillables = $this->loadBindings();
        if ($this->___associative) {
            foreach ($fillables as $key => $value) {
                if (\array_key_exists($value, $attributes) && $this->isNotGuarded($value, $setGuarded)) {
                    $this->setAttribute($key, $attributes[$value]);
                }
            }
        } else {
            foreach ($fillables as $key) {
                if (\array_key_exists($key, $attributes) && $this->isNotGuarded($key, $setGuarded)) {
                    $this->setAttribute($key, $attributes[$key]);
                }
            }
        }
        return $this;
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

    // #region Private methods

    /**
     * Internal attribute setter method.
     *
     * @param mixed $value
     *
     * @return $this
     */
    private function setAttribute(string $name, $value)
    {
        if ($this->hasPropertySetter($name)) {
            return $this->callPropertySetter($name, $value);
        }
        $default = function () use ($name, $value) {
            $this->setRawAttribute($name, $value);
        };
        // If the current class instance  implements {@see CastsAware} interface
        // we calls {@see CastsAware::setCastableProperty} method to set property value
        // using it cast conterpart
        if (interface_exists(CastsAware::class) && $this instanceof CastsAware && (null !== ($this->getCasts()[$name] ?? null))) {
            return $this->setCastableProperty($name, $value, $default);
        }
        // Else set the raw property value
        return $default();
    }

    /**
     * Indicates wheter json attribute definition is an
     * associative array or not along the list of property mappings.
     *
     * @return array
     */
    private function loadBindings()
    {
        return $this->getJsonableAttributes();
    }

    private function hasPropertyGetter($name)
    {
        return method_exists($this, 'get' . Str::camelize($name) . 'Attribute');
    }

    private function hasPropertySetter($name)
    {
        return method_exists($this, 'set' . Str::camelize($name) . 'Attribute');
    }
    // #endregion Private methods
}
