<?php

namespace Drewlabs\PHPValue\Functions;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\Value as ValueTrait;
use Drewlabs\PHPValue\Value;

if (!function_exists('CreateValue')) {

    /**
     * Create a immutable object
     * 
     * @param array $properties 
     * @return Value 
     */
    function CreateValue(array $properties)
    {
        $object = (new class implements ValueInterface
        {
            use ValueTrait;

            /**
             * List of properties defines on the current class
             * 
             * @var string
             */
            private $__PROPERTIES__; 

            public function useProperties(array $properties)
            {
                $this->__PROPERTIES__ = $properties;
                $this->initialize();
                return $this;
            }
        });
        return $object->useProperties($properties);
    }
}