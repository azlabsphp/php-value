<?php

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ResolveModelAware;
use Drewlabs\PHPValue\Contracts\ValueInterface;

use Drewlabs\PHPValue\Traits\ModelAwareValue;

/** @package Drewlabs\PHPValue\Tests\Stubs */
class TestResolvableValueModel implements ValueInterface, ResolveModelAware
{
    use ModelAwareValue;

    public function resolveModel()
    {
        return new TestModel;
    }
}
