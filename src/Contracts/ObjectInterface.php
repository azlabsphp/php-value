<?php


namespace Drewlabs\PHPValue\Contracts;

interface ObjectInterface
{
    /**
     * Get value for property `$name`
     * 
     * @param string $name
     * 
     * @return mixed 
     */
    public function getPropertyValue(string $name);

    /**
     * Check if property exists on a the object
     * 
     * @param string $name
     * 
     * @return bool 
     */
    public function propertyExists(string $name): bool;

    /**
     * Set the property `$name` value to equal provided `$value`
     * 
     * @template T
     * 
     * @param string $name 
     * @param T $value 
     * 
     * @return void 
     */
    public function setPropertyValue(string $name, $value);
}