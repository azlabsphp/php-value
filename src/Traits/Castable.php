<?php

namespace Drewlabs\Immutable\Traits;

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Immutable\Cast;
use Drewlabs\Immutable\Exceptions\JsonEncodingException;

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

    public function castAttribute(string $key, $value)
    {
        return (new Cast($this))($key, $value);
    }

    /**
     * Cast the given attribute to JSON.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    private function castAttributeAsJson($key, $value)
    {
        $value = $this->asJson($value);
        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $this,
                $key,
                json_last_error_msg()
            );
        }
        return $value;
    }

    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    private function asJson($value)
    {
        return json_encode($value);
    }
}
