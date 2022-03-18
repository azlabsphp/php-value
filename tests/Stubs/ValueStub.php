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

use Drewlabs\Immutable\Contracts\CastsAware;
use Drewlabs\Immutable\Traits\Castable;
use Drewlabs\Immutable\Value;

class ValueStub extends Value implements CastsAware
{
    use Castable;

    private $___casts = [
        'message' => 'value:' . Message::class
    ];

    protected $___properties = [
        'name',
        'address',
        'message'
    ];
}
