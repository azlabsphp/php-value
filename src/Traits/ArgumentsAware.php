<?php

namespace Drewlabs\Immutable\Traits;

trait ArgumentsAware
{
    private $arguments = [];

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments ?? $this->arguments ?? [];
        return $this;
    }

    public function getArguments()
    {
        return $this->arguments ?? [];
    }
}