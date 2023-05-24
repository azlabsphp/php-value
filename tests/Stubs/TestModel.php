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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ObjectInterface;

class TestModel implements ObjectInterface
{
    /**
     * Class properties
     * 
     * @var string[]
     */
    private $__PROPERTIES__ = ['title', 'label', 'comments'];

    /**
     * Class attributes
     * 
     * @var (string|string[][])[]
     */
    private $__DICT__ = [
        'title' => 'Welcome to IT World',
        'label' => 'Hello World!',
        'comments' => [
            [
                'title' => 'HW issues',
                'content' => 'Hello World issues',
            ],
        ],
    ];

    public function getPropertyValue(string $name)
    {
        return $this->__get($name);
    }

    public function propertyExists(string $name): bool
    {
        return in_array($name, $this->__PROPERTIES__);
    }

    public function setPropertyValue(string $name, $value)
    {
        return $this->__DICT__[$name] = $value;
    }

    public function __get($name)
    {
        return array_merge($this->getRelations(), $this->attributesToArray())[$name] ?? null;
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
        return 'examples';
    }

    public function attributesToArray()
    {
        return $this->__DICT__ ?? [];
    }

    public function getAttributes()
    {
        return array_merge($this->__DICT__, $this->getRelations());
    }

    public function toArray()
    {
        return $this->__DICT__;
    }

    /**
     * @return TestModelRelation1[]
     */
    public function getRelations()
    {
        return [
            'person' => new TestModelRelation1(),
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
