<?php

namespace Drewlabs\Immutable\Exceptions;

use RuntimeException;

class JsonEncodingException extends RuntimeException
{

    /**
     * Create a new JSON encoding exception for an attribute.
     *
     * @param  mixed  $model
     * @param  mixed  $key
     * @param  string  $message
     * @return static
     */
    public static function forAttribute($model, $key, $message)
    {
        $class = get_class($model);
        return new static("Unable to encode attribute [{$key}] for model [{$class}] to JSON: {$message}.");
    }
}
