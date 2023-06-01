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


interface AbstractPrototype
{
    /**
     * returns the list of owned properties
     *
     * @return string[]
     */
    public function getOwnedProperties();

    /**
     * Add a list of properties to the base objected properties.
     *
     * @return static
     */
    public function addProperties(array $properties = []);

    /**
     * Returns a list of not owned properties for the current value.
     *
     * @return array
     */
    public function getNotOwnedProperties();
}
