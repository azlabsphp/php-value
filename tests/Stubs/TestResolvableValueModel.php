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

class TestResolvableValueModel implements ValueInterface
{
    use ObjectAdapter;

    private const __PROPERTIES__ = [];

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

    public function getAdaptable()
    {
        return new TestModel();
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
