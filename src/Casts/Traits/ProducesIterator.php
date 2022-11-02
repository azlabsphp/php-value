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

namespace Drewlabs\PHPValue\Casts\Traits;

use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Contracts\CollectionInterface;
use function Drewlabs\PHPValue\Functions\CreateValue;

use Drewlabs\PHPValue\Traits\BaseTrait;

trait ProducesIterator
{
    /**
     * Creates an iterable from the value to transform.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     *
     * @return \Generator<int, mixed, mixed, BaseTrait|void>
     */
    protected function createIterable(string $name, $value, ?CastsAware $model = null)
    {
        $value = $value ?? ($model ? $model->getRawAttributes()[$name] : null) ?? null;
        if (
            \is_object($value) &&
            (method_exists($value, 'getIterator') || $value instanceof CollectionInterface) &&
            is_iterable($result = $value->getIterator())
        ) {
            $iterable = $result;
        } else {
            $iterable = $value;
        }
        if (!is_iterable($iterable)) {
            throw new \InvalidArgumentException(sprintf("%s must has getIterator(): \Traversable  method or implements %s interface", \get_class($value)));
        }
        $instance = trim($this->arguments[0] ?? '');

        $properties = empty($this->arguments) ? array_keys($value) : array_values($this->arguments);

        if (empty($this->arguments)) {
            return CreateValue(array_keys($value))->copy($value);
        }

        $creatorFunction = !class_exists($instance) ? static function ($item) use ($properties) {
            return CreateValue($properties)->copy($item);
        }
        : static function ($item) use ($instance) {
            return new $instance($item);
        };

        foreach ($iterable as $current) {
            yield $creatorFunction($current);
        }
    }
}
