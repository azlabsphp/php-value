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

namespace Drewlabs\PHPValue\Traits;

trait IteratorAware
{
    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        // If except columns are provided, we merge the except columns with the hidden columns
        // if order to filter them from the ouput dictionary
        $expects = $this->getHidden();
        foreach ($this->getProperties() as $name) {
            if (!empty(array_intersect($expects, [$name, $this->getRawProperty($name)]))) {
                continue;
            }
            // Each property value is passed though the serialization pipe for it to be casted if
            // a cast or an serialization function is declared for it
            yield $name => $this->callPropertyGetter($name, $this->getRawAttribute($name));
        }
    }

    /**
     * Provides an object oriented iterator over the this object keys and values.
     *
     * @return \Traversable
     */
    public function each(\Closure $callback)
    {
        foreach ($this->getIterator() as $key => $value) {
            $callback($value, $key);
        }
    }
}
