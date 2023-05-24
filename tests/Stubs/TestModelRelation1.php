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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\Adaptable;

class TestModelRelation1 implements Adaptable
{
    /**
     * Class properties.
     *
     * @var string[]
     */
    private $__PROPERTIES__ = ['name', 'score'];

    /**
     * Class attributes.
     *
     * @var (float|string)[]
     */
    private $__DICT__ = [
        'name' => 'TEST PERSON',
        'score' => .72,
    ];

    public function __get($name)
    {
        return $this->toArray()[$name] ?? null;
    }

    public function getPropertyValue(string $name)
    {
        return $this->__get($name);
    }

    public function propertyExists(string $name): bool
    {
        return \in_array($name, $this->__PROPERTIES__, true);
    }

    public function setPropertyValue(string $name, $value)
    {
        return $this->__DICT__[$name] = $value;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getKey()
    {
        return 1;
    }

    public function getTable()
    {
        return 'persons';
    }

    public function attributesToArray()
    {
        return $this->__DICT__ ?? [];
    }

    public function getAttributes()
    {
        return $this->__DICT__;
    }

    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->getRelations());
    }

    /**
     * @return TestModelRelation2[]
     */
    public function getRelations()
    {
        return [
            'profile' => new TestModelRelation2(),
        ];
    }

    public function getHidden()
    {
        return [];
    }

    public function setHidden(array $values)
    {
    }
}
