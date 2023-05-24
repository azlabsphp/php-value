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

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\PHPValue\Exceptions\ImmutableValueException;

trait ArrayAccess
{
    public function __isset($offset)
    {
        // Add isset() method to value interface
        return $this->hasRawAttribute($offset) && null !== $this->getRawAttribute($offset);
    }

    public function __unset($offset)
    {
        throw new ImmutableValueException(__CLASS__);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        // Add has() method to value interface
        return $this->hasRawAttribute($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (\is_int($offset)) {
            return;
        }

        return $this->__get($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableValueException Use the {copyWith} method to create
     *                                 a new object from the properties of the current object while changing the
     *                                 needed properties
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new ImmutableValueException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableValueException Use the {copyWith} method to create
     *                                 a new object from the properties of the current object while changing the
     *                                 needed properties to null
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new ImmutableValueException(__CLASS__);
    }
}
