<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\PHPValue\Traits;

use Drewlabs\Core\Helpers\Arr;
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
     * @return Accessible
     */
    final public function getRawAttributes()
    {
        return $this->__ATTRIBUTES__;
    }

    public function attributesToArray(array $expects = [])
    {
        // If except columns are provided, we merge the except columns with the hidden columns
        // if order to filter them from the ouput dictionary
        [$properties, $expects, $attributes] = [$this->getProperties(), array_unique(array_merge($this->getHidden(), $expects)), $this->getRawAttributes()];

        return Arr::create((function () use ($properties, $expects, $attributes) {
            foreach ($properties as $key => $value) {
                if (!empty(array_intersect($expects, [$key, $value]))) {
                    continue;
                }
                // Each property value is passed though the serialization pipe for it to be casted if
                // a cast or an serialization function is declared for it
                yield $key => $this->callPropertyGetter($key, $this->getFromArrayAttribute($key, $attributes, $properties));
            }
        })());
    }

    /**
     * @return self
     */
    private function setRawAttributes($attributes)
    {
        $this->__ATTRIBUTES__ = $attributes ?? clone $this->__ATTRIBUTES__ ?? new Accessible();

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
     * Get attribute value from the local __ATTRIBUTES__ array.
     *
     * @param array $attributes
     * @param array $properties
     *
     * @return mixed
     */
    private function getFromArrayAttribute(string $name, $attributes = [], $properties = [])
    {
        $attributes = !empty($attributes) ? $attributes : $this->getRawAttributes();
        if (null !== ($value = $attributes[$name] ?? null)) {
            return $value;
        }

        $properties = !empty($properties) ? $properties : $this->getProperties() ?? [];
        $key = Arr::search($name, $properties);
        if ($key && ($value = $attributes[$key])) {
            return $value;
        }

        return null;
    }
}
