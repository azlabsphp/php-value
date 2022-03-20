<?php

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Str;
use InvalidArgumentException;

trait ModelAwareValue
{
    use Proxy, Castable, BaseTrait;

    /**
     * Model instance attached to the current object
     * 
     * @var mixed
     */
    private $___model;

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

    private function createFromModelInstance(object $attributes)
    {
        try {
            $this->setModel($attributes);
            $this->mergeHidden($attributes->getHidden());
            $this->setAttributes($attributes->toArray());
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
        return $this->___model ?? null;
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
        [$model, $attributes, $hidden] = [$this->getModel(), $this->attributesToArray(), $this->getHidden()];
        $relations = method_exists($model, 'getRelations') ? call_user_func([$model, 'getRelations']) : [];
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

    final protected function getRawAttribute(string $name)
    {
        [$properties, $attributes] = [$this->getProperties() ?? [], $this->getRawAttributes()];
        if (!$this->___associative) {
            return $attributes[$name];
        }
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
