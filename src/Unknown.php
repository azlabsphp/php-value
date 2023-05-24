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

namespace Drewlabs\PHPValue;

/**
 * @template TValue
 * @template-covariant TValue
 */
final class Unknown
{
    /**
     * @var TValue
     */
    private $value;

    /**
     * Creates class instances.
     *
     * @param TValue $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Object desrtuctor.
     *
     * @return void
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * Clone the current instance.
     *
     * @return void
     */
    public function __clone()
    {
        // We only care of cloning the boxed variable if the variable is an object
        if ($this->isObject()) {
            $this->value = clone $this->value;
        }
    }

    /**
     * Unset the current object. If the current instance is unset
     * the internal variable value is also unset.
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->unset();
    }

    /**
     * Creates new class instances.
     *
     * @param TValue $value
     *
     * @return self
     */
    public static function new($value)
    {
        return new self($value);
    }

    /**
     * Checks is the boxed variable is a PHP scalar.
     *
     * @return bool
     */
    public function isScalar()
    {
        return \is_scalar($this->value);
    }

    /**
     * Checks if the boxed variable is an integer value.
     *
     * @return bool
     */
    public function isInt()
    {
        return \is_int($this->value);
    }

    /**
     * Checks if the boxed variable is an floating point or decimal value.
     *
     * @return bool
     */
    public function isNumeric()
    {
        return is_numeric($this->value);
    }

    /**
     * Checks if the boxed variable is a string.
     *
     * @return bool
     */
    public function isString()
    {
        return \is_string($this->value);
    }

    /**
     * Check if the boxed variable is an array.
     *
     * @return bool
     */
    public function isArray()
    {
        return \is_array($this->value);
    }

    /**
     * Check if the boxed variable is a callable instance.
     *
     * @return bool
     */
    public function isCallable()
    {
        return !\is_string($this->value) && \is_callable($this->value);
    }

    /**
     * Check if the boxed variable is an object.
     *
     * @return bool
     */
    public function isObject()
    {
        return \is_object($this->value);
    }

    /**
     * Check if the boxed variable has null value.
     *
     * @return bool
     */
    public function isNull()
    {
        return null === $this->value;
    }

    /**
     * Check if the boxed value is an instance of the class string provided.
     *
     * @return bool
     */
    public function is(string $class)
    {
        return is_a($this->value, $class, false);
    }

    /**
     * Check if the boxed variable is a subclass of provided `$class`.
     *
     * @return bool
     */
    public function isSubclassOf(string $class)
    {
        return is_subclass_of($this->value, $class, false);
    }

    /**
     * Returns the boxed variable.
     *
     * @return TValue
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Unset the boxed variable value.
     *
     * @return void
     */
    public function unset()
    {
        unset($this->value);
    }
}
