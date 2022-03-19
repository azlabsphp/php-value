<?php

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Traits\ArgumentsAware;

use function Drewlabs\PHPValue\Functions\CreateValue;

/** @package Drewlabs\PHPValue */
class Value implements CastPropertyInterface
{
    use ArgumentsAware;

    public function set(string $name, $value, ?CastsAware $model = null)
    {
        return [$name => $value];
    }

    public function get(string $name, $value, ?CastsAware $model = null)
    {
        if (empty($this->arguments)) {
            return CreateValue(array_keys($value))->copy($value);
        }
        $model_ = trim($this->arguments[0] ?? '');
        if (!class_exists($model_)) {
            return CreateValue(
                empty($this->arguments) ?
                    array_keys($value) :
                    array_values($this->arguments)
            )->copy($value);
        }
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
