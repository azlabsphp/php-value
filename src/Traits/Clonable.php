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
        if ($this->__ATTRIBUTES__) {
            $this->__ATTRIBUTES__ = clone $this->__ATTRIBUTES__;
        }
        if ($this->__GET__PROPERTY__VALUE__) {
            $this->__GET__PROPERTY__VALUE__ = clone $this->__GET__PROPERTY__VALUE__;
        }
    }
}