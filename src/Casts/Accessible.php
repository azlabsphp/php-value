<?php

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Traits\ArgumentsAware;

use Drewlabs\PHPValue\Accessible as AccessibleClass;

/** @package Drewlabs\PHPValue */
class Accessible implements CastPropertyInterface
{
    use ArgumentsAware;

    public function set(string $name, $value, ?CastsAware $model = null)
    {
        return [$name => $value instanceof AccessibleClass ? $value->toArray() : $value];
    }

    public function get(string $name, $value, ?CastsAware $model = null)
    {
        $accessible = new AccessibleClass;
        $value = null === $value ? $model->getRawAttributes()[$name] ?? null : $value;
        if (is_object($value)) {
            return $accessible->merge(get_object_vars($value));
        }
        if (is_array($value)) {
            return $accessible->merge($value);
        }
        return $accessible;
    }
}
