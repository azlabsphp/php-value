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
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Accessible;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Exceptions\ImmutableValueException;

trait BaseTrait
{
    use ArrayAccess, Clonable, IteratorAware, AttributesAware;

    /**
     * @var bool
     */
    private $__ASSOCIATIVE__ = false;

    /**
     * 
     * 
     * @var \Closure&object
     */
    private $__GET__PROPERTY__VALUE__;

    /**
     * Makes class attributes accessible through -> syntax.
     *
     * @param  $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return ($this->__GET__PROPERTY__VALUE__)($name, $this->getRawAttribute($name));
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
    public function copyWith(array $attributes)
    {
        // Clone the current object to make default copy of it
        return $this->clone()->setAttributes($attributes);
    }

    /**
     * @description Create an instance of class from a standard PHP class
     */
    public function fromStdClass($object_)
    {
        $properties = $this->getProperties();
        foreach ($properties as $key => $value) {
            if (null !== ($value_ = ($object_->{$value} ?? null))) {
                $this->setAttribute($key, $value_);
            }
        }
        return $this;
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
        return ($this->__GET__PROPERTY__VALUE__)(
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

    /**
     * Merge object attributes.
     * 
     * @param array|mixed $attributes 
     * @return BaseTrait
     */
    public function merge($attributes = [])
    {
        return $this->setAttributes(
            is_object($attributes) ?
                (method_exists($attributes, 'toArray') ?
                    $attributes->toArray() :
                    get_object_vars($attributes)) : (is_array($attributes) ?
                    $attributes : [])
        );
    }

    /**
     * Copy object properties changing existing property values with
     * user provided ones.
     * 
     * @param array|mixed $attributes 
     * @return self 
     */
    public function copy($attributes = [])
    {
        return $this->clone()->setAttributes(
            is_object($attributes) ?
            (method_exists($attributes, 'toArray') ?
                $attributes->toArray() :
                get_object_vars($attributes)) : (is_array($attributes) ?
                $attributes : [])
        );
    }


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    // #region Protected & Private methods defintions
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
    protected function initialize()
    {
        $this->___attributes = new Accessible;
        $this->__ASSOCIATIVE__ = Arr::isallassoc($this->getJsonableAttributes());
        $this->__GET__PROPERTY__VALUE__ = Functional::memoize(function(...$args) {
            return $this->callPropertyGetter(...$args);
        });
        return $this;
    }

    /**
     * Attributes setter internal method.
     *
     *
     * @return self
     */
    protected function setAttributes(array $attributes)
    {
        $properties = $this->getProperties();
        foreach ($properties as $key => $value) {
            if (null !== ($value_ = ($attributes[$value] ?? null))) {
                $this->setAttribute($key, $value_);
            }
        }
        return $this;
    }

    /**
     * @internal Internal attribute setter method.
     *
     * @param mixed $value
     *
     * @return self|mixed
     */
    protected function setAttribute(string $name, $value)
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
    final protected function getProperties()
    {
        $properties = $this->getJsonableAttributes() ?? [];
        return !$this->__ASSOCIATIVE__ ? array_combine($properties, $properties) : $properties;
    }

    private function hasPropertyGetter($name)
    {
        return method_exists($this, 'get' . Str::camelize($name) . 'Attribute');
    }

    private function hasPropertySetter($name)
    {
        return method_exists($this, 'set' . Str::camelize($name) . 'Attribute');
    }


    private function setPropertiesValue($attributes)
    {
        if (\is_array($attributes)) {
            $this->setAttributes($attributes);
        } elseif (\is_object($attributes) || ($attributes instanceof \stdClass)) {
            $this->fromStdClass($attributes);
        }
    }
    // #endregion Protected & Private methods defintions
}
