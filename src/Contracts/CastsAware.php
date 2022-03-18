<?php

namespace Drewlabs\Immutable\Contracts;

interface CastsAware
{
    /**
     * Returns list of properties that can be casted
     * 
     * @return array 
     */
    public function getCasts();

    /**
     * Set list of properties that can be casted
     * 
     * @param array $value 
     * @return self|mixed
     */
    public function setCasts(array $value);

    /**
     * Returns the list of raw attributes of the object
     * 
     * @return array|mixed
     */
    public function getRawAttributes();
}