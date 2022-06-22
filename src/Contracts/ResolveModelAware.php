<?php

namespace Drewlabs\PHPValue\Contracts;

interface ResolveModelAware
{
    /**
     * Defines implementation for creating model instance attached
     * to the value object
     * 
     * @return mixed 
     */
    public function resolveModel();
}