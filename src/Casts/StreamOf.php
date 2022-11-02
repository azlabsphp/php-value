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

namespace Drewlabs\PHPValue\Casts;

use Drewlabs\PHPValue\Contracts\CastsAware;

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
        return \function_exists('\Drewlabs\Support\Proxy\Stream') ?
            \call_user_func('\Drewlabs\Support\Proxy\Stream', $iterable) :
            $iterable;
    }
}
