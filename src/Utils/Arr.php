<?php

namespace Drewlabs\PHPValue\Utils;

use ArrayAccess;
use Closure;
use Drewlabs\Core\Helpers\Iter;
use Drewlabs\PHPValue\Contracts\AbstractPrototype;
use Drewlabs\PHPValue\Contracts\HiddenAware;
use Drewlabs\PHPValue\Contracts\ValueInterface;
use JsonSerializable;

/** @package Drewlabs\PHPValue */
class Arr implements AbstractPrototype, HiddenAware, JsonSerializable, ArrayAccess
{
    use Collectable;

    /**
     * Collection class constructor
     * 
     * @param array $items
     * @param Closure(ValueInterface $value, array $properties, array $hidden): mixed $map
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
        $this->items[$offset] =  $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_map(function (ValueInterface $item) {
            return call_user_func_array($this->map, [$item, $this->properties, $this->hidden]);
        }, $this->items);
    }
}
