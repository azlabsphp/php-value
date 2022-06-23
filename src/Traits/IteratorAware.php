<?php

namespace Drewlabs\PHPValue\Traits;

trait IteratorAware
{
    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        // If except columns are provided, we merge the except columns with the hidden columns
        // if order to filter them from the ouput dictionary
        [$properties, $expects, $attributes] = [$this->getProperties(), $this->getHidden(), $this->getRawAttributes()];
        foreach ($properties as $key => $value) {
            if (!empty(\array_intersect($expects, [$key, $value]))) {
                continue;
            }
            // Each property value is passed though the serialization pipe for it to be casted if
            // a cast or an serialization function is declared for it
            yield $key => $this->callPropertyGetter($key, $this->getFromArrayAttribute($key, $attributes, $properties));
        }
    }

    /**
     * Provides an object oriented iterator over the this object keys and values.
     *
     * @return \Traversable
     */
    public function each(\Closure $callback)
    {
        return $this->getRawAttributes()->each($callback);
    }
}
