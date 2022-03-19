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
    protected $___attributes;

    /**
     * @return array
     */
    final public function getRawAttributes()
    {
        return $this->___attributes;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttributes($attributes)
    {
        $this->___attributes = $attributes ?? clone $this->___attributes ?? new Accessible;
        return $this;
    }

    private function mergeRawAttributes(array $attributes = [])
    {
        $this->___attributes->merge($attributes);
        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    private function setRawAttribute(string $name, $value)
    {
        $this->___attributes[$name] = $value;

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