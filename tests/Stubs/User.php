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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\ObjectAdapter;

/**
 * @property bool        $isVerified
 * @property string      $password
 * @property string      $username
 * @property UserDetails $details
 * @property array       $roles
 */
class User implements ValueInterface
{
    use ObjectAdapter;

    protected $__CASTS__ = [
        'isVerified' => 'bool',
        'details' => 'value: '.UserDetails::class,
        'roles' => 'array',
    ];

    protected $__HIDDEN__ = [
        'password',
    ];

    protected $__PROPERTIES__ = [
        'username',
        'password',
        'isVerified',
        'details',
        'roles',
    ];
}
