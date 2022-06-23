<?php

namespace Drewlabs\PHPValue\Contracts;

interface Serializable
{
    /**
     * Convert object back to dictionnary used to create it
     * 
     * @return array|object 
     */
    public function serialize();

    /**
     * Returns a JSON encoded string from the current object
     * 
     * @return string 
     */
    public function toJson();
}