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

interface HiddenAware
{
    /**
     * returns the list of hidden properties.
     *
     * @return string[]
     */
    public function getHidden();

    /**
     * set properties hidden properties.
     *
     * @return string[]
     */
    public function setHidden(array $values);
}
