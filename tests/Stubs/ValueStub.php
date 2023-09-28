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

use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Traits\Castable;
use Drewlabs\PHPValue\Traits\ObjectAdapter;

class ValueStub implements CastsAware
{
    use Castable;
    use ObjectAdapter;

    private const __PROPERTIES__ = [
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

    /**
     * Creates class instance.
     *
     * @param array|Adaptable|Accessible $adaptable
     */
    public function __construct($adaptable = null)
    {
        // code...
        $this->bootInstance(static::__PROPERTIES__, $adaptable);
    }

    /**
     * returns properties cast definitions.
     *
     * @return array
     */
    public function getCasts()
    {
        // code...
        return $this->__CASTS__ ?? [];
    }

    /**
     * set properties cast definitions.
     *
     * @return string[]
     */
    public function setCasts(array $values)
    {
        // code...
        $this->__CASTS__ = $values ?? $this->__CASTS__ ?? [];

        return $this;
    }

    /**
     * returns the list of hidden properties.
     *
     * @return string[]
     */
    public function getHidden()
    {
        // code...
        return $this->__HIDDEN__ ?? [];
    }

    /**
     * set properties hidden properties.
     *
     * @return string[]
     */
    public function setHidden(array $values)
    {
        // code...
        $this->__HIDDEN__ = $values;

        return $this;
    }
}
