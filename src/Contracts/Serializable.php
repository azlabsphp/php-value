<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue\Contracts;

interface Serializable
{
    /**
     * Convert object back to dictionnary used to create it.
     *
     * @return array|object
     */
    public function serialize();

    /**
     * Returns a JSON encoded string from the current object.
     *
     * @return string
     */
    public function toJson();
}
