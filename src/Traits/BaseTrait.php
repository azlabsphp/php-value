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

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Exceptions\ImmutableValueException;
use Drewlabs\PHPValue\Contracts\ValueInterface;

/**
 * @property string[] __PROPERTIES__
 *
 * @implements \Drewlabs\PHPValue\Contracts\ValueInterface
 * @mixin \Drewlabs\PHPValue\Traits\Macroable
 * @mixin \Drewlabs\PHPValue\Traits\Castable
 * @mixin \Drewlabs\PHPValue\Contracts\HiddenAware
 * @mixin \Drewlabs\PHPValue\Contracts\CastsAware
 * @mixin \Drewlabs\PHPValue\Contracts\ValueInterface
 */
trait BaseTrait
{
    use ArrayAccess;

    /**
     * @var \Closure&object
     */
    private $__GET__PROPERTY__VALUE__;

    /**
     * Map of object property -> input property.
     *
     * @var array<string,string>
     */
    private $__PROP__MAP__ = [];

    /**
     * List of properties added to the current object that are not in the current
     * object base definition.
     *
     * @var array<string>
     */
    private $__MISC__PROPERTIES__ = [];

    /**
     * List of properties owned by the current object
     * 
     * @var array
     */
    private $__OWN__PROPERTIES__ = [];

    /**
     * Makes class attributes accessible through -> syntax.
     *
     * @param $name
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
        if (\in_array($name, ['__HIDDEN__', '__PROPERTIES__', '__CASTS__'], true)) {
            return $this->$name = $value;
        }
        throw new ImmutableValueException(__CLASS__);
    }

    /**
     * Creates new class instance.
     *
     * @param mixed ...$args
     *
     * @return static|ValueInterface
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }

    /**
     * Merge object attributes.
     *
     * @return BaseTrait
     */
    public function merge(array $attributes = [])
    {
        return $this->setAttributes($attributes);
    }

    /**
     * Copy object properties changing existing attributes from values from `$attributes` parameter.
     *
     * @return static
     */
    public function copy(array $attributes = [])
    {
        return $this->clone()->setAttributes($attributes);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function attributesToArray(array $expects = [])
    {
        // If except columns are provided, we merge the except columns with the hidden columns
        // if order to filter them from the ouput dictionary
        [$objProps, $expects] = [$this->getProperties(), array_unique(array_merge($this->getHidden(), $expects))];
        $fn = function () use ($objProps, $expects) {
            foreach ($objProps as $name) {
                if (!empty(array_intersect($expects, [$name, $this->getRawProperty($name)]))) {
                    continue;
                }
                // Each property value is passed though the serialization pipe for it to be casted if
                // a cast or an serialization function is declared for it
                yield $name => $this->callPropertyGetter($name, $this->getRawAttribute($name));
            }
        };

        return iterator_to_array($fn());
    }

    public function getAttribute(string $key, $default = null)
    {
        return ($this->__GET__PROPERTY__VALUE__)(
            $key,
            $this->getRawAttribute($key),
            \is_callable($default) ? $default : static function () use ($default) {
                return $default;
            }
        );
    }

    // #region Properties updates
    private function getProperties()
    {
        return array_unique(array_merge($this->getOwnedProperties() ?? [], $this->getNotOwnedProperties() ?? []));
    }

    /**
	 * returns the list of owned properties
	 *
	 * @return string[]
	 */
	public function getOwnedProperties()
	{
		# code...
		return $this->__OWN__PROPERTIES__ ?? [];
	}


    /**
     * Add a list of properties to the base objected properties.
     *
     * @return static
     */
    public function addProperties(array $properties = [])
    {
        $this->__MISC__PROPERTIES__ = array_unique(array_merge($this->getNotOwnedProperties(), array_diff($properties, $this->getOwnedProperties() ?? [])));

        return $this;
    }

    /**
     * Returns a list of not owned properties for the current value.
     *
     * @return array
     */
    public function getNotOwnedProperties()
    {
        return $this->__MISC__PROPERTIES__ ?? [];
    }
    // #endregion Properties updates

    // #region Protected & Private methods defintions
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    private function callPropertySetter($name, $value)
    {
        $result = $this->{$this->propertySetterName($name)}($value);
        if (null !== $result) {
            $this->setRawAttribute($name, $result);
        }

        return $this;
    }

    private function callPropertyGetter($name, $value, \Closure $default = null)
    {
        if ($this->hasPropertyGetter($name)) {
            return $this->{$this->propertyGetterName($name)}($value);
        }
        $default = static function () use ($value, $default) {
            return null === $value ? ($default ? $default() : $value) : $value;
        };
        // If the current object is instance of {@see CastsAware} and interface
        // exist {@see CastAware} we call the getCastableProperty method
        if ((interface_exists(CastsAware::class)) && ($this instanceof CastsAware) && (null !== ($this->getCasts()[$name] ?? null))) {
            return $this->getCastableProperty($name, $value, $default);
        }

        return $default();
    }

    /**
     * Attributes setter internal method.
     *
     * @return static
     */
    private function setAttributes(array $attributes)
    {
        // Merge own properties and not owned properties
        foreach ($this->getProperties() as $name) {
            if (null !== ($result = ($attributes[$name] ?? $attributes[$this->getRawProperty($name)] ?? null))) {
                $this->setAttribute($name, $result);
            }
        }

        return $this;
    }

    /**
     * @internal internal attribute setter method
     *
     * @param mixed $value
     *
     * @return static|mixed
     */
    private function setAttribute(string $name, $value)
    {
        // TODO: Query for the raw property value
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

    private function hasPropertyGetter($name)
    {
        return method_exists($this, $this->propertyGetterName($name));
    }

    private function hasPropertySetter($name)
    {
        return method_exists($this, $this->propertySetterName($name));
    }

    private function buildPropsDefinitions(array $properties)
    {
        // In case the properties attribute is not a dictionnary, we loop through each keys and
        // create a key=>value representation of the properties
        $objProps = [];
        foreach ($properties as $key => $value) {
            $key = is_numeric($key) ? $value : $key;
            $objProps[$key] = $value;
        }
        // Make properties definitions associative to uniformize
        // handlers
        $this->__PROP__MAP__ = $objProps;
        $this->__OWN__PROPERTIES__ = array_keys($objProps);
    }

    /**
     * Return the raw property name corresponding to the `$name` property.
     *
     * @return string
     */
    private function getRawProperty(string $name)
    {
        return $this->__PROP__MAP__[$name] ?? $name;
    }

    /**
     * Construct `$name` property setter name.
     *
     * @return string
     */
    private function propertySetterName(string $name)
    {
        return 'set'.Str::camelize($name).'Attribute';
    }

    /**
     * Construct `$name` property getter name.
     *
     * @return string
     */
    private function propertyGetterName(string $name)
    {
        return 'get'.Str::camelize($name).'Attribute';
    }
    // #endregion Protected & Private methods defintions
}
