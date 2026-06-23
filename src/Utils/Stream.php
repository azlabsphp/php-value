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
use Drewlabs\PHPValue\Traits\Proxy;

class Stream implements AbstractPrototype, HiddenAware, \JsonSerializable
{
    use Collectable;
    use Proxy;

    /**
     * collection class constructor.
     *
     * @param mixed                       $items
     * @param \Closure(ValueInterface $value, array $properties, array $hidden): mixed $map
     */
    public function __construct($items, \Closure $map)
    {
        $this->items = $items;
        $this->map = $map;
    }

    /**
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     */
    public function __call($name, $arguments)
    {
        return $this->proxy($this->items, $name, $arguments);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->items->map(function (ValueInterface $item) {
            return \call_user_func_array($this->map, [$item, $this->properties, $this->hidden]);
        });
    }

    /**
     * Returns the list of items.
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }
}
