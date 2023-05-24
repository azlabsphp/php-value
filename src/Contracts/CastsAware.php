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

interface CastsAware
{
    /**
     * Returns list of properties that can be casted.
     *
     * @return array
     */
    public function getCasts();

    /**
     * Set list of properties that can be casted.
     *
     * @return self|mixed
     */
    public function setCasts(array $value);

    /**
     * Returns raw value for the matching attribute name
     * 
     * @param string $name
     * 
     * @return mixed 
     */
    public function getRawAttribute(string $name);

    /**
     * Get value of a property configured as castable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getCastableProperty(string $key, $value, \Closure $default);

    /**
     * Set value of a property configured as castable.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setCastableProperty(string $key, $value, \Closure $default);
}
