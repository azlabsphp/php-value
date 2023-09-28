<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;

use function Drewlabs\PHPValue\Functions\CreateAdapter;

use Drewlabs\PHPValue\Traits\ArgumentsAware;

class Value implements CastPropertyInterface
{
    use ArgumentsAware;

    public function set(string $name, $value, CastsAware $model = null)
    {
        return [$name => $value];
    }

    public function get(string $name, $value, CastsAware $model = null)
    {
        // First we query for the value using the it property name if the value point to null reference
        $value = $value ?? ($model ? $model->getRawAttribute($name) : null) ?? null;

        // Case the value point to a null reference, we simply return the value without any further action
        if (null === $value) {
            return $value;
        }
        // Case the arguments is empty, we simply create a dynamic value instance
        if (empty($this->arguments)) {
            return CreateAdapter(array_keys($value))->copy($value);
        }
        $props = empty($this->arguments) ? (\is_array($value) ? array_keys($value) : []) : array_values(\array_slice($this->arguments ?? [], 1));
        // Case the first item in the argument array is a class we create a new instance of it, else we create a default value object
        // from the attributes
        return !class_exists($instance = trim($this->arguments[0] ?? '')) ? CreateAdapter([$instance, ...$props])->copy($value) : new $instance($value, ...$props);
    }
}
