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
 * @property array  $details
 * @property string $firstname
 * @property string $firstname
 */
class UserDetails implements ValueInterface
{
    use ObjectAdapter;

    protected $__PROPERTIES__ = [
        'firstname',
        'lastname',
        'emails',
    ];

    public function getEmailsAttribute($value)
    {
        return \is_array($value) ? $value : array_filter([$value]);
    }
}
