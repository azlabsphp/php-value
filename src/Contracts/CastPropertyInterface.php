<?php

namespace Drewlabs\PHPValue\Contracts;

interface CastPropertyInterface
{
    /**
     * Set the arguments to use when casting property
     * 
     * @param array $arguments 
     * @return self 
     */
    public function setArguments(array $arguments);

    /**
     * Transform property to it underlying {@see Value}
     *
     * @param string $name 
     * @param mixed $value 
     * @param CastsAware|null $model 
     * @return mixed 
     */
    public function set(string $name, $value, ?CastsAware $model = null);

    /**
     * Transform property from it underlying {@see Value}
     * 
     * @param string $name 
     * @param mixed $value 
     * @param CastsAware|null $model 
     * @return mixed 
     */
    public function get(string $name, $value, ?CastsAware $model = null);
}