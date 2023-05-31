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

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\ObjectAdapter as ValueTrait;
use Drewlabs\PHPValue\ObjectAdapter;

if (!\function_exists('CreateAdapter')) {

    /**
     * Create a immutable object.
     *
     * @return ObjectAdapter
     */
    function CreateAdapter(array $properties)
    {
        $object = (new class() implements ValueInterface
        {

            use ValueTrait;

            /**
             * @var array
             */
            private $__HIDDEN__ = [];

            /**
             * @var array
             */
            private $__CASTS__ = [];
        
            public function useProperties(array $properties)
            {
                $this->bootInstance($properties);
                return $this;
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
        });

        return $object->useProperties($properties);
    }
}
