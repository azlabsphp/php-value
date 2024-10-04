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

namespace Drewlabs\PHPValue\Utils;

use Closure;

trait Collectable
{

    /** @var mixed */
    private $items;

    /** @var string[] */
    private $properties = [];

    /** @var string[] */
    private $hidden = [];

    /** @var Closure */
    private $map;

    public function getHidden()
    {
        return $this->hidden;
    }

    public function setHidden(array $values)
    {
        $this->hidden = $values;
        return $this;
    }

    public function getOwnedProperties()
    {
        return [];
    }

    public function addProperties(array $properties = [])
    {
        $this->properties = array_unique(array_merge($this->getNotOwnedProperties(), array_diff($properties, $this->getOwnedProperties() ?? [])));
        return $this;
    }

    public function getNotOwnedProperties()
    {
        return $this->properties;
    }
}
