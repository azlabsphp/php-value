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

use DateTimeImmutable;

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
     * Cast the boxed variable instance into provided type
     * 
     * @param class-string<\T> $type 
     * 
     * @return \T 
     */
    public function cast(string $type)
    {
        switch ($type) {
            case 'int':
                return intval($this->get());
            case 'string':
                return strval($this->get());
            case 'float':
                return floatval($this->get());
            case 'array':
                return $this->castToArray($this->get());
            case 'date':
                return $this->castToDate($this->get());
            default:
                return $this->defaultCast($type, $this->get());
        }
    }

    /**
     * Cast boxed variable to `integer` value type
     * 
     * @return int
     */
    public function toInt()
    {
        return $this->cast('int');
    }

    /**
     * Cast boxed variable to string value type
     * 
     * @return string
     */
    public function toString()
    {
        return $this->cast('string');
    }

    /**
     * Cast boxed variable to `floating point` value type
     * 
     * @param int $precision 
     * @param int $mode 
     * @return float 
     */
    public function toFloat($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        /**
         * @var float
         */
        $result = $this->cast('float');
        return round($result, $precision, $mode);
    }

    /**
     * Cast boxed variable to `tableau` value type
     * 
     * @return string
     */
    public function toArray()
    {
        return $this->cast('array');
    }

    /**
     * Cast boxed variable to PHP `date time` value type
     * 
     * @return \DateTimeInterface
     */
    public function toDate()
    {
        return $this->cast('date');
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
     * Cast the iterable instance to PHP `tableau`
     * @param iterable $value 
     * @return array 
     */
    private function castToArray(iterable $value)
    {
        if ($value instanceof \Traversable) {
            return iterator_to_array($value);
        }
        return (array)($value);
    }

    /**
     * Provides a class instance casting or return the value if class
     * does not exits in the runtime context.
     * 
     * @param class-string<T> $type 
     * @param mixed $value
     * 
     * @return T 
     */
    private function defaultCast(string $type, $value)
    {
        return class_exists($type) ? new $type($value) : $value;
    }

    /**
     * Cast `$value` to PHP date time immutable instance
     * 
     * @param string|int|\DateTimeInterface $value 
     * @return DateTimeImmutable|false 
     */
    private function castToDate($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $value->format('Y-m-d H:i:s.u'), $value->getTimezone());
        }
        if (is_numeric($value)) {
            return \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, date(\DateTimeImmutable::ATOM, (int) $value));
        }
        try {
            // Try to parse using database connection format
            $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        } catch (\InvalidArgumentException $e) {
            // fallback to ISO8601 standard if format does not match database connection format
            $date = \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $value);
        }
        return $date ?: \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $value);
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
}
