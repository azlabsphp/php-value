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
 * A pair which represents a key and an associated value.
 *
 * @property mixed $key
 * @property mixed $value
 *
 * @template-covariant TKey
 * @template-covariant TValue
 */
final class Pair implements \JsonSerializable
{
    /**
     * @var mixed The pair's key
     *
     * @psalm-param TKey $key
     */
    public $key;

    /**
     * @var mixed The pair's value
     *
     * @psalm-param TValue $value
     */
    public $value;

    /**
     * Creates a new instance.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @psalm-param TKey $key
     * @psalm-param TValue $value
     */
    public function __construct($key = null, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param mixed $name
     *
     * @return mixed|null
     */
    public function __isset($name)
    {
        if ('key' === $name || 'value' === $name) {
            return $this->$name !== null;
        }

        return false;
    }

    /**
     * This allows unset($pair->key) to not completely remove the property,
     * but be set to null instead.
     *
     * @return void
     */
    public function __unset(string $name)
    {
        if ('key' === $name || 'value' === $name) {
            $this->$name = null;

            return;
        }
    }

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function __set($name, $value)
    {
        if ('key' === $name || 'value' === $name) {
            $this->$name = $value;

            return;
        }
    }

    /**
     * Returns a representation to be used for var_dump and print_r.
     *
     * @return array
     *
     * @psalm-return array{key: TKey, value: TValue}
     */
    public function __debugInfo()
    {
        return $this->toArray();
    }

    /**
     * Returns a string representation of the pair.
     */
    public function __toString()
    {
        return 'object('.get_class($this).')';
    }

    /**
     * @param mixed $name
     *
     * @return mixed|null
     */
    public function &__get($name)
    {
        if ('key' === $name || 'value' === $name) {
            return $this->$name;
        }
    }

    /**
     * Returns a copy of the Pair.
     *
     * @psalm-return self<TKey, TValue>
     */
    public function copy(): self
    {
        return new static($this->key, $this->value);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-return array{key: TKey, value: TValue}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-return array{key: TKey, value: TValue}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
