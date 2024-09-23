<?php

namespace Drewlabs\PHPValue\Utils;

use Closure;
use Drewlabs\PHPValue\Contracts\AbstractPrototype;
use Drewlabs\PHPValue\Contracts\HiddenAware;
use Drewlabs\PHPValue\Contracts\ValueInterface;
use JsonSerializable;

/** @package Drewlabs\PHPValue */
class Collection implements AbstractPrototype, HiddenAware, JsonSerializable
{
    use Collectable;

    /**
     * Collection class constructor
     * 
     * @param \Illuminate\Collection\Collection|\Drewlabs\Collections\Collection $items
     * @param Closure(ValueInterface $value, array $properties, array $hidden): mixed $map
     */
    public function __construct($items, \Closure $map)
    {
        $this->items = $items;
        $this->map = $map;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->items->map(function (ValueInterface $item) {
            return call_user_func_array($this->map, [$item, $this->properties, $this->hidden]);
        });
    }

    public function __call($name, $arguments)
    {
        return $this->proxy($this->items, $name, $arguments);
    }
}
