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

trait HiddenAware
{
    public function setHidden(array $value)
    {
        $this->__HIDDEN__ = $value;

        return $this;
    }

    public function getHidden()
    {
        return $this->__HIDDEN__ ?? [];
    }

    /**
     * Merge hidden property values.
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->__HIDDEN__ = array_merge($this->getHidden() ?? [], $value ?? []);

        return $this;
    }
}
