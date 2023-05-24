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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\Value;

class Message implements ValueInterface
{
    use Value;

    private $__PROPERTIES__ = [
        'to' => 'To',
        'from' => 'From',
        'logger' => 'Logger',
        'address' => 'Address',
    ];
}
