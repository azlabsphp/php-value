<?php

namespace Drewlabs\PHPValue\Contracts;

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

    /**
     * Get value of a property configured as castable
     * 
     * @param string $key 
     * @param mixed $value 
     * @param \Closure $default
     * @return mixed 
     */
    public function getCastableProperty(string $key, $value, \Closure $default);

    /**
     * Set value of a property configured as castable
     * 
     * @param string $key 
     * @param mixed $value 
     * @param \Closure $default
     * @return self 
     */
    public function setCastableProperty(string $key, $value, \Closure $default);
}