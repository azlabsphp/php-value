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

use Drewlabs\Core\Helpers\Functional;
use Drewlabs\PHPValue\Accessible;
use Drewlabs\PHPValue\Contracts\Adaptable;

trait ObjectAdapter
{
    use BaseTrait;
    use Castable;
    use HiddenAware;
    use IteratorAware;
    use Macroable;
    use Proxy;

    /**
     * Properties container.
     *
     * @var Accessible|Adaptable
     */
    private $__ADAPTABLE__;

    /**
     * Creates class instance
     *
     * @param array|Adaptable|Accessible $adaptable
     */
    public function __construct($adaptable = [])
    {
    }

    /**
     * Boot the class instance
     * 
     * @param array<string>|array<string,string> $props 
     * @param array|Adaptable|Accessible $adaptable 
     * @return void 
     */
    protected function bootInstance($props, $adaptable = [])
    {
        $this->buildPropsDefinitions($props);
        $this->__GET__PROPERTY__VALUE__ = Functional::memoize(function (...$args) {
            return $this->callPropertyGetter(...$args);
        });
        if (\is_array($adaptable)) {
            $this->__ADAPTABLE__ = new Accessible();
            foreach ($adaptable as $key => $value) {
                $this->__ADAPTABLE__->setPropertyValue($key, $value);
            }
        } elseif ($adaptable instanceof Adaptable) {
            $this->__ADAPTABLE__ = $adaptable;
        } else {
            $this->__ADAPTABLE__ = new Accessible();
        }
    }

    // #region Macros
    public function __call($name, $arguments)
    {
        // Call the macro if exists on the current instance
        if ($macro = $this->lookupCallable($name)) {
            return $macro->call(...$arguments);
        }
        // Or proxy method cal to adaptable instance
        return $this->proxy($this->getAdaptable(), $name, $arguments);
    }

    // #region clone
    public function __clone()
    {
        $this->adapt((null !== ($result = $this->getAdaptable())) ? clone $result : $result);
        $this->__GET__PROPERTY__VALUE__ = $this->__GET__PROPERTY__VALUE__ ? clone $this->__GET__PROPERTY__VALUE__ : $this->__GET__PROPERTY__VALUE__;
    }
    // #region clone

    // #region String __repr__
    public function __toString()
    {
        return json_encode($this->toArray(), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }
    // #endregion Macros

    // #region Attributes
    public function hasRawAttribute(string $name)
    {
        return null !== $this->getAdaptable() && $this->getAdaptable()->propertyExists($this->getRawProperty($name));
    }

    public function getRawAttribute(string $name)
    {
        return null !== $this->getAdaptable() ? $this->getAdaptable()->getPropertyValue($this->getRawProperty($name)) : null;
    }
    // #endrefion Attributes

    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function clone()
    {
        return clone $this;
    }
    // #endregion String __repr__

    // #region Miscellanous
    /**
     * Set value properties from PHP object.
     *
     * @return self
     */
    public function fromObject(object $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->setRawAttribute((string) $key, $value);
        }

        return $this;
    }
    // #endregion Miscellaous

    // #region Adaptable getter and setter
    /**
     * Adapt the `$adaptable` instance into the current adapter instance.
     *
     * @return self
     */
    public function adapt(Adaptable $adaptable)
    {
        $this->__ADAPTABLE__ = $adaptable;

        return $this;
    }

    /**
     * Retuns the adaptable instance for the current object.
     *
     * @return Adaptable
     */
    public function getAdaptable()
    {
        return $this->__ADAPTABLE__;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttribute(string $name, $value)
    {
        if (null !== $this->getAdaptable()) {
            $this->getAdaptable()->setPropertyValue($this->getRawProperty($name), $value);
        }

        return $this;
    }
    // #endregion Adaptable getter and setter
}
