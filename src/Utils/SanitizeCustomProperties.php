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

namespace Drewlabs\PHPValue\Utils;

class SanitizeCustomProperties
{
    /**
     * @var bool
     */
    private $sort = false;

    /**
     * Creates class instance.
     */
    public function __construct(bool $sort = false)
    {
        $this->sort = $sort;
    }

    /**
     * Creates list of custom properties names removing `invalid` names
     * from the provided list of values.
     *
     * @param array|iterable|\Traversable $values
     */
    public function __invoke($values): array
    {
        $array = iterator_to_array($this->createIterator($values));
        if ($this->sort) {
            sort($array);
        }

        return $array;
    }

    /**
     * Creates list of custom properties names removing `invalid` names
     * from the provided list of values.
     *
     * @param array|iterable|\Traversable $values
     */
    public function call($values): array
    {
        $array = iterator_to_array($this->createIterator($values));
        if ($this->sort) {
            sort($array);
        }

        return $array;
    }

    /**
     * Creates an iterable of properties.
     *
     * @param array|iterable|\Traversable $properties
     *
     * @return \Traversable<int, mixed, mixed, void>
     */
    private function createIterator($properties)
    {
        $array = [];

        foreach ($properties as $property) {
            // escape the property if it equals * or it's a float, decimal or integer value
            if (('*' === $property) || is_numeric($property)) {
                continue;
            }

            // property names must be a valid `PHP` string
            if (!\is_string($property)) {
                continue;
            }

            // we do not want to inclue duplicated properties, so we exclude the property if it was already handled
            if (false !== array_search($property, $array, true)) {
                continue;
            }

            $array[] = $property;

            // By default yiel the property value as it is
            yield $property;
        }

    }
}
