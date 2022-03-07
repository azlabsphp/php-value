<?php

namespace Drewlabs\Immutable\Functions;

use Drewlabs\Immutable\Value;

if (!function_exists('CreateValueObject')) {

    /**
     * Create a immutable object
     * 
     * @param array $properties 
     * @return Value 
     */
    function CreateValue(array $properties)
    {
        $object = new class extends Value
        {
            public function useProperties(array $properties)
            {
                $this->___properties = $properties;
                $this->initializeAttributes();
                return $this;
            }
        };
        return $object->useProperties($properties);
    }
}
