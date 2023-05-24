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

use Drewlabs\Core\Helpers\Functional;
use Drewlabs\PHPValue\Accessible;
use Drewlabs\PHPValue\Contracts\ObjectInterface;

trait Value
{
    use BaseTrait;
    use Castable;
    use HiddenAware;
    use IteratorAware;


    /**
     * Properties container.
     *
     * @var Accessible|ObjectInterface
     */
    private $__DICT__;

    /**
     * Creates an instance of Drewlabs\PHPValue\ValueObject::class.
     *
     * @param array|object $attributes
     */
    public function __construct($attributes = [])
    {
        $this->buildPropsDefinitions(isset($this->__PROPERTIES__ ) ? $this->__PROPERTIES__  : []);
        $this->__GET__PROPERTY__VALUE__ = Functional::memoize(function (...$args) {
            return $this->callPropertyGetter(...$args);
        });
        if (is_array($attributes)) {
            $this->__DICT__ = new Accessible;
            $this->__DICT__->merge($attributes);
        } else if ($attributes instanceof ObjectInterface) {
            $this->__DICT__ = $attributes;
        } else {
            $this->__DICT__ = new Accessible();
        }
    }


    //#region Attributes
    public function hasRawAttribute(string $name)
    {
        return null !== $this->__DICT__ && $this->__DICT__->propertyExists($this->getRawProperty($name));
    }

    public function getRawAttribute(string $name)
    {
        return $this->__DICT__->getPropertyValue($this->getRawProperty($name));
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttribute(string $name, $value)
    {
        $this->__DICT__->setPropertyValue($this->getRawProperty($name), $value);
        return $this;
    }
    //#endrefion Attributes

    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    //#region clone
    public function __clone()
    {
        $this->__DICT__ = null !== $this->__DICT__ ? clone $this->__DICT__ : $this->__DICT__;
        $this->__GET__PROPERTY__VALUE__ = $this->__GET__PROPERTY__VALUE__ ? clone $this->__GET__PROPERTY__VALUE__ : $this->__GET__PROPERTY__VALUE__;
    }

    public function clone()
    {
        return clone $this;
    }
    //#region clone

    //#region String __repr__
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    //#endregion String __repr__

    //#region Miscellanous
    /**
     * Set value properties from PHP object
     * 
     * @param object $object 
     * 
     * @return self 
     */
    public function fromObject(object $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->setRawAttribute(strval($key), $value);
        }
        return $this;
    }
    //#endregion Miscellaous
}
