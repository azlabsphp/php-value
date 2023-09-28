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

namespace Drewlabs\PHPValue\Exceptions;

class InvalidCastException extends \RuntimeException
{
    /**
     * The name of the affected Eloquent model.
     *
     * @var string
     */
    public $model;

    /**
     * The name of the column.
     *
     * @var string
     */
    public $property;

    /**
     * The name of the cast type.
     *
     * @var string
     */
    public $castType;

    /**
     * Create a new exception instance.
     *
     * @param object $model
     * @param string $property
     * @param string $castType
     *
     * @return static
     */
    public function __construct($model, $property, $castType)
    {
        $class = $model::class;
        parent::__construct("Call to undefined cast [{$castType}] on property [{$property}] in model [{$class}].");
        $this->model = $class;
        $this->property = $property;
        $this->castType = $castType;
    }
}
