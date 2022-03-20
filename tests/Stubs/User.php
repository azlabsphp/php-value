<?php

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\Value;

/**
 * @property bool $isVerified
 * @property string $password
 * @property string $username
 * @property UserDetails $details
 * @property array $roles
 * 
 * @package Drewlabs\PHPValue\Tests\Stubs
 */
class User implements ValueInterface
{
    use Value;

    protected $__CASTS__ = [
        'isVerified' => 'bool',
        'details' => 'value: ' . UserDetails::class,
        'roles' => 'array'
    ];

    protected $__HIDDEN__ = [
        'password'
    ];

    protected $__PROPERTIES__ = [
        'username',
        'password',
        'isVerified',
        'details',
        'roles'
    ];
}