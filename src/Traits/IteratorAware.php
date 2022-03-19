<?php

namespace Drewlabs\PHPValue\Traits;

trait IteratorAware
{
    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        [$properties, $hidden] = [$this->getProperties(), $this->getHidden()];
        foreach ($properties as $key => $value) {
            if (!\in_array($key, $hidden, true)) {
                yield $value => $this->callPropertyGetter($key, $this->getRawAttribute($key));
            }
        }
    }

    /**
     * Provides an object oriented iterator over the this object keys and values.
     *
     * @return \Traversable
     */
    public function each(\Closure $callback)
    {
        return $this->getAttributes()->each($callback);
    }
}