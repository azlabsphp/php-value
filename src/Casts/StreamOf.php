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

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Utils\Stream;

class StreamOf extends CollectionOf
{
    /**
     * {@inheritDoc}
     *
     * @param \Traversable $value
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function get(string $name, $value, ?CastsAware $model = null)
    {
        $iterable = $this->createIterable($name, $value, $model);

        return new Stream(
            \Drewlabs\Collections\Streams\Stream::of($this->createIterable($name, $value, $model)),
            static function ($item, array $properties = [], array $hidden = []) {
                return $item->addProperties($properties)->setHidden(array_merge($item->getHidden(), $hidden));
            }
        );
    }
}
