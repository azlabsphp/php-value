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
        $object = (new class() implements ValueInterface {
            use ValueTrait;

            /**
             * List of properties defines on the current class.
             *
             * @var string
             */
            private $__PROPERTIES__;

            public function useProperties(array $properties)
            {
                $this->buildPropsDefinitions($properties);

                return $this;
            }
        });

        return $object->useProperties($properties);
    }
}
