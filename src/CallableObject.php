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

namespace Drewlabs\PHPValue;

/**
 * @template-covariant TKey
 * @template-covariant TValue of callable
 */
class CallableObject
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable|\Closure(...$args):mixed
     */
    private $callback;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Creates class instance.
     */
    public function __construct(string $name, callable $callback, array $parameters = [])
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * Creates a new callable instnance.
     *
     * @param mixed $callback
     *
     * @return CallableObject<mixed, callable>
     */
    public static function new($callback)
    {
        return new self(uniqid('callable').time(), $callback, []);
    }

    /**
     * Invoke the callback with a list of arguments.
     *
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public function call(...$arguments)
    {
        $arguments = empty($arguments) ? $this->getParameters() : $arguments;

        return ($this->callback)(...$arguments);
    }

    /**
     * Returns the callable callback instance.
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Returns the name of the callable instance.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the list of parameters attached to the callable instance.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters ?? [];
    }

    /**
     * Set the callback or function to execute when the current instance is called.
     *
     * @return void
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Set the list of parameters to pass to the callable when calling this instance.
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
