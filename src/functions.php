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

namespace Drewlabs\PHPValue\Functions;

use Drewlabs\PHPValue\Contracts\Adaptable;
use Drewlabs\PHPValue\Contracts\HiddenAware;
use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\ObjectAdapter;
use Drewlabs\PHPValue\Traits\ObjectAdapter as ValueTrait;

if (!\function_exists('CreateAdapter')) {

    /**
     * Create a immutable object.
     *
     * @return ObjectAdapter
     */
    function CreateAdapter(array $properties, Adaptable $instance = null)
    {
        return new class($properties, $instance) implements ValueInterface, HiddenAware {
            use ValueTrait;

            /**
             * @var array
             */
            private $__HIDDEN__ = [];

            /**
             * @var array
             */
            private $__CASTS__ = [];

            /**
             * Create new class instance.
             */
            public function __construct(array $properties, Adaptable $instance = null)
            {
                $this->bootInstance($properties, $instance);
            }

            /**
             * returns properties cast definitions.
             *
             * @return array
             */
            public function getCasts()
            {
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
                return $this->__HIDDEN__ ?? [];
            }

            /**
             * set properties hidden properties.
             *
             * @return string[]
             */
            public function setHidden(array $values)
            {
                $this->__HIDDEN__ = $values;

                return $this;
            }
        };
    }
}
