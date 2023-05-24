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

interface Adaptable
{
    /**
     * Get value for property `$name`.
     *
     * @return mixed
     */
    public function getPropertyValue(string $name);

    /**
     * Check if property exists on a the object.
     */
    public function propertyExists(string $name): bool;

    /**
     * Set the property `$name` value to equal provided `$value`.
     *
     * @template T
     *
     * @param T $value
     *
     * @return void
     */
    public function setPropertyValue(string $name, $value);
}
