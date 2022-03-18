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

use Drewlabs\Contracts\Data\Model\Model;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\Immutable\Traits\Proxy;
use Drewlabs\Immutable\Traits\ValueObject;

/**
 * Enhance the default {@see ValueObject} class with model bindings.
 */
abstract class ModelValue
{
    use Proxy;
    use ValueObject;

    /**
     * @var Model
     */
    private $___model;

    /**
     * @param \stdObject|Model|array $attributes
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->initializeAttributes();
        if ($attributes instanceof Model) {
            $this->createFromModelInstance($attributes);
        } else {
            $this->setProperties($attributes);
        }
    }

    private function createFromModelInstance(Model $attributes)
    {
        // TODO : SET MODEL INSTANCE FOR CLASS USER TO MANIPULATE DURING SERIALISATIOM
        $this->setModel($attributes);
        $this->setHidden(
            array_merge(
                $attributes->getHidden() ?? [],
                $this->getHidden() ?? []
            )
        );
        // TODO : CREATE ATTRIBUTE FROM MODEL SERIALIZATION
        $this->setAttributes($attributes->toArray());
    }

    private function setProperties($attributes)
    {
        $this->initializeAttributes();
        if (\is_array($attributes)) {
            $this->setAttributes($attributes);
        } elseif (\is_object($attributes) || ($attributes instanceof \stdClass)) {
            $this->fromStdClass($attributes);
        }
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

    public function __call($name, $arguments)
    {
        $model = $this->getModel();
        if ($model) {
            return $this->proxy($model, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on " . __CLASS__);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->___model;
    }

    /**
     * @param mixed $model
     *
     * @return self
     */
    public function setModel($model)
    {
        if ($model) {
            $this->___model = $model;
        }

        return $this;
    }

    public function toArray()
    {
        $attributes = $this->attributesToArray();
        $hidden = array_merge($this->getHidden());
        $relations = method_exists($this->___model, 'getRelations') ?
            call_user_func([$this->___model, 'getRelations']) :
            [];
        // TODO: GET MODEL RELATIONS
        foreach ($relations as $key => $value) {
            if (in_array($key, $hidden)) {
                continue;
            }
            // TODO: Provide a better implementation to avoid performance heck or
            // remove implementation that strip hidden sub attributes as it can impact 
            // application performance for large datasets.
            $props = [];
            foreach ($hidden as $k => $v) {
                if (Str::startsWith($v, $key)) {
                    $props[] = Str::after("$key.", $v);
                    unset($hidden[$k]);
                    continue;
                }
            }
            $attributes[$key] = Arr::except($value->attributesToArray(), $props);
            // #endregion TODO
            // $attributes[$key] = $value;
        }
        return $attributes;
    }
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return Accessible|mixed
     */
    final protected function getAttributes()
    {
        return $this->___attributes;
    }
}
