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
        if ($value = $this->getRawAttributes()) {
            $this->setRawAttributes(clone $value);
        }
        if ($property = $this->__GET__PROPERTY__VALUE__) {
            $this->__GET__PROPERTY__VALUE__ = clone $property;
        }
    }
}