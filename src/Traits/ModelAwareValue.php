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
     * @param \Drewlabs\Contracts\Data\Model\Model|array|mixed $attributes
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
        return $this->__MODEL__ ?? null;
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
        return array_merge($attributes, Arr::create($this->relationsIterator($relations, $hidden)));
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
            yield $property => $this->callPropertyGetter($property, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray(array $expects = [])
    {
        [$properties, $hidden, $attributes] = [$this->getProperties(), $this->getHidden(), $this->getRawAttributes()];
        return iterator_to_array((function () use ($hidden, $properties, $expects, $attributes) {
            foreach ($properties as $key => $value) {
                if (\in_array($key, $hidden, true) || in_array($key, $expects)) {
                    continue;
                }
                yield $value => $this->callPropertyGetter($key, $this->getRawAttribute($key, $attributes));
            }
        })());
    }

    final protected function getRawAttribute(string $name, $attributes = [])
    {
        [$properties, $attributes] = [$this->getProperties() ?? [], !empty($attributes) ? $attributes : $this->getRawAttributes()];
        if (null !== ($value = $attributes[$name] ?? null)) {
            return $value;
        }
        $key = Arr::search($name, $properties);
        if ($key && ($value = $attributes[$key])) {
            return $value;
        }
        return null !== ($model = $this->getModel()) ? ($model->{$name} ?? null) : null;
    }
}
