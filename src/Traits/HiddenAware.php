<?php

namespace Drewlabs\PHPValue\Traits;

trait HiddenAware
{
    public function setHidden(array $value)
    {
        $this->___hidden = $value;
        return $this;
    }

    public function getHidden()
    {
        return $this->___hidden ?? [];
    }

    /**
     * Merge hidden property values.
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->___hidden = array_merge($this->getHidden() ?? [], $value ?? []);

        return $this;
    }
}