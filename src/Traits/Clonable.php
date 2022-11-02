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

namespace Drewlabs\PHPValue\Traits;

trait Clonable
{
    public function __clone()
    {
        if ($value = $this->getRawAttributes()) {
            $this->setRawAttributes(clone $value);
        }
        if ($property = $this->__GET__PROPERTY__VALUE__) {
            $this->__GET__PROPERTY__VALUE__ = clone $property;
        }
    }

    public function clone()
    {
        return clone $this;
    }
}
