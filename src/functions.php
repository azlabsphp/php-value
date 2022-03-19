<?php

namespace Drewlabs\Immutable\Functions;

use Drewlabs\Immutable\Contracts\ValueInterface;
use Drewlabs\Immutable\Traits\Value as ValueTrait;
use Drewlabs\Immutable\Value;

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
            private $___properties; 

            public function useProperties(array $properties)
            {
                $this->___properties = $properties;
                $this->initializeAttributes();
                return $this;
            }

            public function getProperties()
            {
                return $this->___properties;
            }
        });
        return $object->useProperties($properties);
    }
}