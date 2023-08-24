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

    private const __PROPERTIES__ = [
        'username',
        'password',
        'isVerified',
        'details',
        'roles',
    ];
	
    /**
	 * Creates class instance
	 * 	
	 * @param array|Adaptable|Accessible $adaptable
	 * 
	 */
	public function __construct($adaptable = null)
	{
		# code...
		$this->bootInstance(static::__PROPERTIES__, $adaptable);
	}

	/**
	 * returns properties cast definitions
	 * 
	 *
	 * @return array
	 */
	public function getCasts()
	{
		# code...
		return $this->__CASTS__ ?? [];
	}

	/**
	 * set properties cast definitions
	 * 
	 * @param array $values
	 *
	 * @return string[]
	 */
	public function setCasts(array $values)
	{
		# code...
		$this->__CASTS__ = $values ?? $this->__CASTS__ ?? [];
		return $this;
	}

	/**
	 * returns the list of hidden properties
	 * 
	 *
	 * @return string[]
	 */
	public function getHidden()
	{
		# code...
		return $this->__HIDDEN__ ?? [];
	}

	/**
	 * set properties hidden properties
	 * 
	 * @param array $values
	 *
	 * @return string[]
	 */
	public function setHidden(array $values)
	{
		# code...
		$this->__HIDDEN__ = $values;
		return $this;
	}

	private function getAppends()
	{
		return ['email'];
	}
}
