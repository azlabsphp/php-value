<?php

namespace Drewlabs\PHPValue\Contracts;

interface CastsInboundProperties
{
    /**
     * Transform property to it underlying {@see Value}
     *
     * @param string $name 
     * @param mixed $value 
     * @param CastsAware|null $model 
     * @return mixed 
     */
    public function set(string $name, $value, ?CastsAware $model = null);
}