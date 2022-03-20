<?php

namespace Drewlabs\PHPValue\Traits;

trait HiddenAware
{
    public function setHidden(array $value)
    {
        $this->__HIDDEN__ = $value;
        return $this;
    }

    public function getHidden()
    {
        return $this->__HIDDEN__ ?? [];
    }

    /**
     * Merge hidden property values.
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->__HIDDEN__ = array_merge($this->getHidden() ?? [], $value ?? []);

        return $this;
    }
}