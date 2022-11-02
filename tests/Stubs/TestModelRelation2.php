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

class TestModelRelation2
{
    public function __get($name)
    {
        return array_merge([], $this->attributesToArray())[$name] ?? null;
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
        return 'profiles';
    }

    public function attributesToArray()
    {
        return [
            'person_id' => 1,
            'url' => 'https://picsum.photos/id/1/200/300',
        ];
    }

    public function getAttributes()
    {
        return [
            'person_id' => 1,
            'url' => 'https://picsum.photos/id/1/200/300',
        ];
    }

    public function toArray()
    {
        return [
            'person_id' => 1,
            'url' => 'https://picsum.photos/id/1/200/300',
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
