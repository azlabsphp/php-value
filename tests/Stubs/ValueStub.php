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
    use Castable;
    use Value;

    protected $__PROPERTIES__ = [
        'name',
        'address',
        'message',
        'likes',
        'errors',
    ];

    private $__CASTS__ = [
        'message' => 'value:'.Message::class,
        'likes' => 'arrayOf:'.LikeStub::class,
        'errors' => 'streamOf:'.ErrorStub::class,
    ];
}
