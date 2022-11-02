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

use Drewlabs\PHPValue\Casts\Traits\ProducesIterator;
use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Traits\ArgumentsAware;

class ArrayOf implements CastPropertyInterface
{
    use ArgumentsAware;
    use ProducesIterator;

    public function set(string $name, $value, ?CastsAware $model = null)
    {
        if (\is_string($value) || null === $value || \is_bool($value) || is_numeric($value)) {
            $value = array_filter([$value], static function ($item) {
                return null !== $item;
            });
        }

        return [$name => $value];
    }

    public function get(string $name, $value, ?CastsAware $model = null)
    {
        return iterator_to_array($this->createIterable($name, $value, $model));
    }
}
