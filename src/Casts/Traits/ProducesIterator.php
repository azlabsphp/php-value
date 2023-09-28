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

namespace Drewlabs\PHPValue\Casts\Traits;

use Drewlabs\PHPValue\Contracts\CastsAware;

use function Drewlabs\PHPValue\Functions\CreateAdapter;

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
    protected function createIterable(string $name, $value, CastsAware $model = null)
    {
        $value = $value ?? ($model ? $model->getRawAttribute($name) : null) ?? null;
        $iterable = \is_object($value) && (method_exists($value, 'getIterator') || $value instanceof \IteratorAggregate) && is_iterable($result = $value->getIterator()) ? $result : $value;
        if (!is_iterable($iterable)) {
            throw new \InvalidArgumentException(sprintf("%s must has getIterator(): \Traversable  method or implements %s interface", get_class($value)));
        }
        if (empty($this->arguments)) {
            return CreateAdapter(array_keys($value))->copy($value);
        }
        /**
         * @var array $props
         */
        $props = empty($this->arguments) ? array_keys($value) : array_values(\array_slice($this->arguments ?? [], 1));
        $fn = !class_exists($instance = trim($this->arguments[0] ?? '')) ? static function ($item) use ($props, $instance) {
            return CreateAdapter([$instance, ...$props])->copy($item);
        }
        : static function ($item) use ($instance, $props) {
            return new $instance($item, ...$props);
        };
        foreach ($iterable as $current) {
            yield $fn($current);
        }
    }
}
