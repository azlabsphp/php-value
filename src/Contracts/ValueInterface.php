<?php

namespace Drewlabs\PHPValue\Contracts;

use Drewlabs\Contracts\Clonable;

/** @package Drewlabs\PHPValue\Contracts */
interface ValueInterface extends CastsAware, \JsonSerializable, \ArrayAccess, Clonable, \IteratorAggregate
{
    /**
     * @description Create a PHP Array from properties of the current object.
     * It works like PHP global function {@see get_object_vars}
     *
     * @return array
     */
    public function toArray();

    /**
     * @description Creates a new instance of the class by copying existing
     * class property values while modifying them with values of specified property names
     * passed by the caller
     * 
     * @return self 
     */
    public function copy(array $attributes = []);
}