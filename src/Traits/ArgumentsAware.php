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

trait ArgumentsAware
{
    private $arguments = [];

    /**
     * Set the list of current object arguments.
     *
     * @return mixed
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments ?? $this->arguments ?? [];

        return $this;
    }

    /**
     * Return the list of current object arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments ?? [];
    }
}
