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

interface CastsInboundProperties
{
    /**
     * Transform property to it underlying {@see Value}.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function set(string $name, $value, ?CastsAware $model = null);
}
