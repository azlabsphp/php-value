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

use Drewlabs\Immutable\Value;

class Message extends Value
{
    protected function getJsonableAttributes()
    {
        return [
            'to' => 'To',
            'from' => 'From',
            'logger' => 'Logger',
            'address' => 'Address',
        ];
    }
}
