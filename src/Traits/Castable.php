<?php

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Cast;

/**
 * @description Provides composed class with properties and methods
 * for dealing with cast operations on properties
 */
trait Castable
{

    public function getCasts()
    {
        return property_exists($this, '___casts') ? $this->___casts : [];
    }

    public function setCasts(array $value)
    {
        $this->___casts = $value ?? $this->___casts ?? [];
        return $this;
    }

    public function getCastableProperty(string $key, $value, \Closure $default)
    {
        $cast =  new Cast($this);
        $value = $cast->__invoke($key, $value);
        return  null !== $value ? $value : $default();
    }

    public function setCastableProperty(string $key, $value, \Closure $default)
    {
        $cast =  new Cast($this);
        if ($cast->isClosureCastable($key)) {
            return $default();
        }
        // Evaluate if property is enum castable
        if ($cast->isEnumCastable($key)) {
            return $this->setRawAttribute($key, $cast->computeEnumCastablePropertyValue($key, $value));
        }
        if ($cast->isClassCastable($key)) {
            return $this->mergeRawAttributes($cast->computeClassCastablePropertyValue($key, $value) ?? []);
        }

        if (null !== $value && $cast->isJsonCastable($key)) {
            return $this->setRawAttribute($key, $cast->computePropertyAsJson($key, $value));
        }
        if (Str::contains($key, '->')) {
            return $this->setRawAttribute($key, $this->computeJsonAttributeAtPath($key, $value));
        }
        return $default();
    }



    /**
     * Set a given JSON attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function computeJsonAttributeAtPath($key, $value)
    {
        [$key, $path] = explode('->', $key, 2);

        $value = json_encode(
            $this->updateValueAtPath(
                $path,
                $key,
                $value
            )
        );
        return $value;
    }

    /**
     * Get an array attribute with the given key and value set.
     *
     * @param  string  $path
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    private function updateValueAtPath($path, $key, $value)
    {
        return Functional::tap($this->getArrayAttributeByKey($key), function (&$array) use ($path, $value) {
            Arr::set($array, str_replace('->', '.', $path), $value);
        });
    }

    /**
     * Get an array attribute or return an empty array if it is not set.
     *
     * @param  string  $key
     * @return array
     */
    private function getArrayAttributeByKey($key)
    {
        if (null === $this->getRawAttribute($key)) {
            return [];
        }

        return json_decode(
            $this->getRawAttribute($key),
            true
        );
    }
}
