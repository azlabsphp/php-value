<?php

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\PHPValue\Accessible;

trait AttributesAware
{

    use HiddenAware;

    /**
     * Properties container.
     *
     * @var object|\ArrayAccess|\JsonSerializable|array
     */
    protected $__ATTRIBUTES__;

    /**
     * @return array
     */
    final public function getRawAttributes()
    {
        return $this->__ATTRIBUTES__;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttributes($attributes)
    {
        $this->__ATTRIBUTES__ = $attributes ?? clone $this->__ATTRIBUTES__ ?? new Accessible;
        return $this;
    }

    private function mergeRawAttributes(array $attributes = [])
    {
        $this->__ATTRIBUTES__->merge($attributes);
        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttribute(string $name, $value)
    {
        $this->__ATTRIBUTES__[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray()
    {
        return iterator_to_array((function () {
            foreach ($this->getRawAttributes() as $key => $value) {
                if (!\in_array($key, $this->getHidden(), true)) {
                    yield $key => $value;
                }
            }
        })());
    }
}