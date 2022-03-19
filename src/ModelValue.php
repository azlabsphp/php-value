<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue;

use Drewlabs\Contracts\Clonable;
use Drewlabs\Contracts\Support\Immutable\ValueObjectInterface;
use Drewlabs\PHPValue\Traits\ModelAwareValue;

/**
 * Enhance the default {@see ValueObject} class with model bindings.
 */
abstract class ModelValue implements ValueObjectInterface, Clonable, \IteratorAggregate
{
    use ModelAwareValue;
}
