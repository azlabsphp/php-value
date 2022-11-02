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

use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsAware;
use function Drewlabs\PHPValue\Functions\CreateValue;

use Drewlabs\PHPValue\Traits\ArgumentsAware;

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
        $instance = trim($this->arguments[0] ?? '');
        if (!class_exists($instance)) {
            return CreateValue(empty($this->arguments) ? array_keys($value) : array_values($this->arguments))
                ->copy($value);
        }

        return $instance ?
            new $instance(
                $value ??
                    ($model ? $model->getRawAttributes()[$name] : null)
                    ?? null,
                ...\array_slice($this->arguments ?? [], 1)
            ) :
            $value;
    }
}
