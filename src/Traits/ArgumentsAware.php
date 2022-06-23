<?php

namespace Drewlabs\PHPValue\Traits;

trait ArgumentsAware
{
    private $arguments = [];

    /**
     * Set the list of current object arguments
     * 
     * @param array $arguments 
     * @return mixed 
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments ?? $this->arguments ?? [];
        return $this;
    }

    /**
     * Return the list of current object arguments
     * 
     * @return array 
     */
    public function getArguments()
    {
        return $this->arguments ?? [];
    }
}