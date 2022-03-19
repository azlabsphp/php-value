<?php

namespace Drewlabs\PHPValue\Traits;

trait Clonable
{
    public function clone()
    {
        return clone $this;
    }

    public function __clone()
    {
        if ($this->___attributes) {
            $this->___attributes = clone $this->___attributes;
        }
    }
}