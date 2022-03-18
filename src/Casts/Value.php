<?php

namespace Drewlabs\Immutable\Casts;

use Drewlabs\Immutable\Contracts\CastPropertyInterface;
use Drewlabs\Immutable\Contracts\CastsAware;
use Drewlabs\Immutable\Traits\ArgumentsAware;

/** @package Drewlabs\Immutable */
class Value implements CastPropertyInterface
{
    use ArgumentsAware;

    public function set(string $name, $value, ?CastsAware $model = null)
    {
        return [$name => $value];
    }

    public function get(string $name, $value, ?CastsAware $model = null)
    {
        $model_ = !empty($this->arguments) && class_exists($this->arguments[0]) ?
            $this->arguments[0] :
            null;
        return $model_ ?
            new $model_(
                $value ??
                    ($model ? $model->getRawAttributes()[$name] : null)
                    ?? null,
                ...array_slice($this->arguments ?? [], 1)
            ) :
            $value;
    }
}
