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

    protected $___casts = [
        'isVerified' => 'bool',
        'details' => 'value: ' . UserDetails::class,
        'roles' => 'array'
    ];

    protected $___hidden = [
        'password'
    ];

    protected $___properties = [
        'username',
        'password',
        'isVerified',
        'details',
        'roles'
    ];
}