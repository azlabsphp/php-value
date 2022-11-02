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

interface CollectionInterface
{
    /**
     * Returns an instance of the PHP iterable.
     *
     * @return \Traversable|array
     */
    public function getIterator();
}
