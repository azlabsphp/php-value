<?php

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\Value;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * @property array $details
 * @property string $firstname
 * @property string $firstname
 * @package Drewlabs\PHPValue\Tests\Stubs
 */
class UserDetails implements ValueInterface
{
    use Value;

    protected $__PROPERTIES__ = [
        'firstname',
        'lastname',
        'emails'
    ];

    public function getEmailsAttribute($value)
    {
        return is_array($value) ? $value: array_filter([$value]);
    }

}