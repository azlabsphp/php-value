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

use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Traits\Castable;
use Drewlabs\PHPValue\Traits\Value;

class ValueStub implements CastsAware
{
    use Castable, Value;

    private $___casts = [
        'message' => 'value:' . Message::class
    ];

    protected $___properties = [
        'name',
        'address',
        'message'
    ];
}
