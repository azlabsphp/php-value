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
use Drewlabs\PHPValue\Contracts\ObjectInterface;

trait ModelAware
{
    use BaseTrait;
    use Castable;
    use Proxy;
    use HiddenAware;
    use IteratorAware;

    /**
     * Model instance attached to the current object.
     *
     * @var ObjectInterface
     */
    private $__MODEL__;

    /**
     * Creates class instances
     * 
     * @param ObjectInterface|mixed $attributes
     *
     */
    public function __construct(ObjectInterface $instance = null)
    {
        if ($instance) {
            $this->setModel($instance);
        }
        $this->buildPropsDefinitions(isset($this->__PROPERTIES__ ) ? $this->__PROPERTIES__  : []);
        $this->__GET__PROPERTY__VALUE__ = Functional::memoize(function (...$args) {
            return $this->callPropertyGetter(...$args);
        });
    }

    //#region Model methods
    public function __call($name, $arguments)
    {
        if ($model = $this->getModel()) {
            return $this->proxy($model, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on " . __CLASS__);
    }

    /**
     * @return ObjectInterface
     */
    public function getModel()
    {
        if (null !== ($instance = $this->__MODEL__)) {
            return $instance;
        }
        try {
            // Try calling resolveModel() implementation on the instance
            // It will throw an exception if the model does not implements
            // ResolveAware interface or does not defines resolveModel()
            // implementation
            return $this->resolveModel() ?? null;
        } catch (\Throwable $e) {
            // We simply return a null if the model can not be resolved
            return null;
        }
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function setModel(ObjectInterface $value)
    {
        $this->__MODEL__ = $value;
        return $this;
    }
    //#endregion Model methods

    public function toArray()
    {
        return $this->attributesToArray();
    }

    //#region clone
    public function __clone()
    {
        try {
            if ($instance = $this->getModel()) {
                $this->setModel(clone $instance);
            }
            $this->__GET__PROPERTY__VALUE__ = $this->__GET__PROPERTY__VALUE__ ? clone $this->__GET__PROPERTY__VALUE__ : $this->__GET__PROPERTY__VALUE__;
        } catch (\Throwable $e) {
            // Case failure, we move forward
        }
    }
    public function clone()
    {
        return clone $this;
    }
    //#region 

    //#region Serialization
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    //#endregion Serialization

    // #region Attributes
    public function hasRawAttribute(string $name)
    {
        return $this->getModel()->propertyExists($this->getRawProperty($name));
    }

    public function getRawAttribute(string $name)
    {
        return $this->getModel()->getPropertyValue($this->getRawProperty($name)) ?? null;
    }

    private function setRawAttribute(string $name, $value)
    {
        $this->getModel()->setPropertyValue($this->getRawProperty($name), $value);
        return $this;
    }
    // #endregion Attributes

    //#region String __repr__
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    //#endregion String __repr__
}
