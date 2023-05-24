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

use Drewlabs\PHPValue\CallableObject;

trait Macroable
{
    /**
     * @var array<CallableObject>
     */
    private $__MACROS__ = [];

    /**
     * Bind a function to execute whenever PHP runtime tries to resolve a
     * method to call on the current class.
     *
     * @template TReturn of self
     *
     * @return TReturn
     */
    public function bind(string $method, callable $callback, array $parameters = [])
    {
        if ($callable = $this->lookupCallable($method)) {
            $callable->setCallback(\Closure::fromCallable($callback)->bindTo($this));
            $callable->setParameters($parameters);
        } else {
            $this->__MACROS__[] = new CallableObject($method, \Closure::fromCallable($callback)->bindTo($this), $parameters);
        }

        return $this;
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @psalm-return CallableObject<TKey, callable>|null
     */
    protected function lookupCallable(string $name)
    {
        foreach ($this->__MACROS__ as $value) {
            if ($value->getName() === $name) {
                return $value;
            }
        }
    }
}
