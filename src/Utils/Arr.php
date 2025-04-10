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

namespace Drewlabs\PHPValue\Utils;

use Drewlabs\PHPValue\Contracts\AbstractPrototype;
use Drewlabs\PHPValue\Contracts\HiddenAware;
use Drewlabs\PHPValue\Contracts\ValueInterface;

class Arr implements AbstractPrototype, HiddenAware, \JsonSerializable, \ArrayAccess
{
    use Collectable;

    /**
     * Collection class constructor.
     *
     * @param \Closure(ValueInterface $value, array $properties, array $hidden): mixed $map
     */
    public function __construct(array $items, \Closure $map)
    {
        $this->items = $items;
        $this->map = $map;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items, $offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_map(function (ValueInterface $item) {
            return \call_user_func_array($this->map, [$item, $this->properties, $this->hidden]);
        }, $this->items);
    }

    /**
     * Returns the list of items.
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
