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

namespace Drewlabs\Immutable\Traits;

use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Immutable\Accessible;
use Drewlabs\Immutable\Exceptions\ImmutableValueException;

trait ValueObject
{
    use ArrayAccess;

    /**
     * List of jsonnable properties for the given object
     * 
     * @var array
     */
    protected $___properties = [];

    /**
     * Attribute container.
     *
     * @var object
     */
    protected $___attributes;

    /**
     * Defines the properties that can not been set using the attr array.
     *
     * @var array
     */
    protected $___guarded = [];

    /**
     * List of properties to hide when jsonSerializing the current object.
     *
     * @var array
     */
    protected $___hidden = [];

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
        return $this->_internalGetAttribute($name);
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
        throw new ImmutableValueException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->___attributes->__toString();
    }

    protected function __internalSerialized()
    {
        $fillables = $this->loadBindings();
        if ($this->___associative) {
            return iterator_to_array(
                (function () use ($fillables) {
                    foreach ($fillables as $key => $value) {
                        if (!\in_array($key, $this->___hidden, true)) {
                            yield $value => $this->callAttributeSerializer($key);
                        }
                    }
                })()
            );
        }

        return iterator_to_array(
            (function () use ($fillables) {
                foreach (array_values($fillables) as $key) {
                    if (!\in_array($key, $this->___hidden, true)) {
                        yield $key => $this->callAttributeSerializer($key);
                    }
                }
            })()
        );
    }

    /**
     * {@inheritDoc}
     *
     * Creates a copy of the current object changing the changing old attributes
     * values with newly proivided ones
     */
    public function copyWith(array $attributes, $setGuarded = false)
    {
        $attributes = array_merge(
            $this->__internalSerialized(),
            $attributes
        );

        return (clone $this)->initializeAttributes()
            ->setAttributes(
                $attributes,
                $setGuarded
            );
    }

    /**
     * {@inheritDoc}
     *
     * Create an instance of {ValueObject} from a standard PHP class
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
     *
     * JSON Serializable method definition. It convert
     * class attributes to a json object aka PHP array, string, object etc...
     */
    public function jsonSerialize()
    {
        return $this->__internalSerialized();
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray()
    {
        return iterator_to_array((function () {
            foreach ($this->___attributes as $key => $value) {
                if (!\in_array($key, $this->___hidden, true)) {
                    yield $key => $value;
                }
            }
        })());
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->jsonSerialize();
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
        $callback = function ($name, $default_) {
            $result = drewlabs_core_array_get(
                $this->___attributes ? $this->___attributes->toArray() : [],
                $name,
                function () use ($name) {
                    return $this->__get($name);
                }
            );

            return $result ?? (\is_callable($default_) ? (new \ReflectionFunction($default_))->invoke() : $default_);
        };

        return $this->_propertyGetterExists($key) ?
            $this->callAttributeSerializer($key) ?? (\is_callable($default) ?
                (new \ReflectionFunction($default))->invoke() :
                $default) : $callback($key, $default);
    }

    public function setHidden(array $value)
    {
        $this->___hidden = $value;

        return $this;
    }

    public function getHidden()
    {
        return $this->___hidden;
    }

    /**
     * Merge hidden property values.
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->___hidden = array_merge($this->___hidden ?: [], $value ?: []);

        return $this;
    }

    /**
     * Merge object attributes.
     * 
     * @param array|\stdClass $attributes 
     * @return ValueObject
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
    protected function callAttributeDeserializer($name, $value)
    {
        if ($this->_propertySetterExists($name)) {
            return $this->{'set' . drewlabs_core_strings_as_camel_case($name) . 'Attribute'}($value);
        }

        return $value;
    }

    protected function callAttributeSerializer($name)
    {
        if ($this->_propertyGetterExists($name)) {
            return $this->{'get' . drewlabs_core_strings_as_camel_case($name) . 'Attribute'}();
        }
        return $this->___attributes[$name] ?? null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNotGuarded($value, bool $load = false)
    {
        return $load ? true : !\in_array($value, $this->___guarded, true);
    }

    /**
     * @return self
     */
    protected function initializeAttributes()
    {
        $this->___attributes = new Accessible;
        $this->___associative = drewlabs_core_array_is_full_assoc(
            $this->getJsonableAttributes()
        );
        return $this;
    }

    /**
     * @return array
     */
    final public function getRawAttributes()
    {
        return $this->___attributes->toArray();
    }

    /**
     * @return mixed
     */
    final protected function getRawAttribute(string $name)
    {
        return $this->___attributes[$name] ?? null;
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
     * Attributes setter internal method.
     *
     * @param bool $setGuarded
     *
     * @return $this
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
        $result = $this->callAttributeDeserializer($name, $value);
        if (null !== $result) {
            $this->___attributes[$name] = $result;
        }

        return $this;
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

    /**
     * Internal attribute setter method.
     * 
     * @param string $name 
     * @return mixed 
     */
    private function _internalGetAttribute(string $name)
    {
        $fillables = $this->loadBindings() ?? [];
        if (!$this->___associative) {
            return $this->callAttributeSerializer($name);
        }
        if (null !== $this->___attributes[$name] ?? null) {
            return $this->callAttributeSerializer($name);
        }
        if (\array_key_exists($name, $fillables)) {
            return $this->callAttributeSerializer($name);
        }
        if ($key = drewlabs_core_array_search($name, $fillables)) {
            return $this->callAttributeSerializer($key);
        }

        return null;
    }

    private function _propertyGetterExists($name)
    {
        return method_exists($this, 'get' . drewlabs_core_strings_as_camel_case($name) . 'Attribute');
    }

    private function _propertySetterExists($name)
    {
        return method_exists($this, 'set' . drewlabs_core_strings_as_camel_case($name) . 'Attribute');
    }

    // #endregion Private methods
}
