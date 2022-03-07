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

namespace Drewlabs\Immutable\Tests\Stubs;

class FileLogger
{
    public $mutable = 'FALSE';

    public function log()
    {
        return 'Logging to the file...';
    }

    public function updateMutable()
    {
        $this->mutable = 'FALSE' === $this->mutable ? 'TRUE' : 'FALSE';
    }
}
