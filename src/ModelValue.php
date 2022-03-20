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

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\ModelAwareValue;

/**
 * Enhance the default {@see ValueObject} class with model bindings.
 */
abstract class ModelValue implements ValueInterface, \IteratorAggregate
{
    use ModelAwareValue;
}
