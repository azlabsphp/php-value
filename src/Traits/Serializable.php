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

trait Serializable
{
    /**
     * Convert object back to dictionnary used to create it
     * 
     * @return array|object 
     */
    public function serialize()
    {
        // If except columns are provided, we merge the except columns with the hidden columns
        // if order to filter them from the ouput dictionary
        [$properties, $expects, $attributes] = [$this->getProperties(), $this->getHidden(), $this->getRawAttributes()];
        return Arr::create((function() use ($properties, $expects, $attributes) {
            foreach ($properties as $key => $value) {
                if (!empty(\array_intersect($expects, [$key, $value]))) {
                    continue;
                }
                // Each property value is passed though the serialization pipe for it to be casted if
                // a cast or an serialization function is declared for it
                yield $value => $this->callPropertyGetter($key, $this->getFromArrayAttribute($key, $attributes, $properties));
            }
        })());
    }

    /**
     * Returns a JSON encoded string from the current object
     * 
     * @return string 
     */
    public function toJson()
    {
        return json_encode($this->serialize());
    }
}
