<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue\Exceptions;

class JsonEncodingException extends \RuntimeException
{
    /**
     * Create a new JSON encoding exception for an attribute.
     *
     * @param mixed  $instance
     * @param mixed  $key
     * @param string $message
     *
     * @return static
     */
    public static function forAttribute($instance, $key, $message)
    {
        $class = get_class($instance);

        return new static(sprintf('Unable to encode attribute [%s] for model [%s] to JSON: %s.', $key, $class, $message));
    }
}
