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

class TestModelRelation1
{
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
        return 'persons';
    }

    public function attributesToArray()
    {
        return [
            'name' => 'TEST PERSON',
            'score' => .72,
        ];
    }

    public function getAttributes()
    {
        return [
            'name' => 'TEST PERSON',
            'score' => .72,
        ];
    }

    public function toArray()
    {
        return [
            'name' => 'TEST PERSON',
            'score' => .72,
            'profile' => new TestModelRelation2(),
        ];
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
