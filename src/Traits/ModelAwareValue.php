<?php

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Arr;
use Generator;
use InvalidArgumentException;

trait ModelAwareValue
{
    use Proxy, Castable, BaseTrait;

    /**
     * Model instance attached to the current object
     *
     * @var mixed
     */
    private $__MODEL__;

    /**
     * @param mixed $attributes
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->initialize();
        if (is_array($attributes)) {
            $this->setPropertiesValue($attributes);
        } else {
            $this->createFromModelInstance($attributes);
        }
    }

    public function __call($name, $arguments)
    {
        if ($model = $this->getModel()) {
            return $this->proxy($model, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on " . __CLASS__);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        if (null !== ($model = $this->__MODEL__)) {
            return $model;
        }
        try {
            // Try calling resolveModel() implementation on the instance
            // It will throw an exception if the model does not implements
            // ResolveAware interface or does not defines resolveModel()
            // implementation
            return method_exists($this, 'resolveModel') ? $this->resolveModel() : null;
        } catch (\Throwable $e) {
            // We simply return a null if the model can not be resolved
            return null;
        }
    }

    /**
     * @param mixed $model
     *
     * @return self
     */
    public function setModel($model)
    {
        if ($model) {
            $this->__MODEL__ = $model;
        }

        return $this;
    }

    public function toArray()
    {
        // First we serialize properties of the value object
        [$attributes, $hidden] = [
            $this->attributesToArray(
                array_keys(
                    $relations = method_exists(
                        ($model = $this->getModel()),
                        'getRelations'
                    ) ? call_user_func([$model, 'getRelations']) : []
                )
            ),
            $this->getHidden()
        ];
        // Then we merge the value own properties and the serialized relation properties
        // declared on the binded model as output
        return array_merge($attributes, Arr::create($this->relationsIterator($relations, $hidden)));
    }

    final protected function getRawAttribute(string $name, $attributes = [])
    {
        if ($value = $this->getFromArrayAttribute($name, $attributes)) {
            return $value;
        }
        return null !== ($model = $this->getModel()) ? ($model->{$name} ?? null) : null;
    }


    /**
     * Set current object from an object model
     *
     * @param null|object $attributes
     * @return void
     * @throws InvalidArgumentException
     */
    private function createFromModelInstance(?object $attributes = null)
    {
        try {
            if ($attributes) {
                $this->setModel($attributes);
                $this->mergeHidden($attributes->getHidden());
                $this->setAttributes($attributes->toArray());
            }
        } catch (\Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     *
     * @param array $relations
     * @param array $hidden
     * @return Generator<string|int, mixed, mixed, void>
     */
    private function relationsIterator(array $relations = [], $hidden = [])
    {
        foreach ($relations as $property => $value) {
            if (in_array($property, $hidden)) {
                continue;
            }
            // Each relation declared on the model is passed through cast or serialization
            // pipe for it to be parsed to the the output
            yield $property => $this->callPropertyGetter($property, $value);
        }
    }
}
