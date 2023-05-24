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

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Accessible as AccessibleClass;
use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;

use Drewlabs\PHPValue\Traits\ArgumentsAware;

class Accessible implements CastPropertyInterface
{
    use ArgumentsAware;

    public function set(string $name, $value, ?CastsAware $model = null)
    {
        return [$name => $value instanceof AccessibleClass ? $value->toArray() : $value];
    }

    public function get(string $name, $value, ?CastsAware $model = null)
    {
        $accessible = new AccessibleClass();
        $value = null === $value ? $model->getRawAttribute($name) ?? null : $value;
        if (\is_object($value)) {
            return $accessible->merge(get_object_vars($value));
        }
        if (\is_array($value)) {
            return $accessible->merge($value);
        }

        return $accessible;
    }
}
