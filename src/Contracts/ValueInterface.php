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

namespace Drewlabs\PHPValue\Contracts;

/**
 * @mixin \Drewlabs\PHPValue\Contracts\HiddenAware
 * @mixin \Drewlabs\PHPValue\Contracts\CastsAware
 */
interface ValueInterface extends CastsAware, \ArrayAccess, \IteratorAggregate, Serializable, AbstractPrototype
{
    /**
     * Returns the parsed value of the provided `$name` attribute.
     *
     * @template TResult
     *
     * @param mixed $default
     *
     * @return TResult
     */
    public function getAttribute(string $name, $default = null);

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
     * @return static
     */
    public function copy(array $attributes = []);
}
