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

namespace Drewlabs\PHPValue\Tests;

use ArrayIterator;
use Drewlabs\PHPValue\Unknown;
use PHPUnit\Framework\TestCase;
use stdClass;

class UnknownTest extends TestCase
{
    public function test_unknown_is_integer()
    {
        $value = Unknown::new(3);
        $this->assertFalse($value->isString());
        $this->assertTrue($value->isInt());
        $this->assertSame(3, $value->get());
    }

    public function test_unknown_is_null()
    {
        $value = Unknown::new(null);
        $this->assertTrue($value->isNull());
        $this->assertFalse($value->isInt());
    }

    public function test_unknown_clone_creates_a_copy_of_the_boxed_variable()
    {
        $object = new stdClass();
        $object->name = 'John Doe';
        $object->age = 29;

        /**
         * @var Unknown<\stdClass>
         */
        $unknow = Unknown::new($object);
        $unknow2 = clone $unknow;
        $value2 = $unknow2->get();
        $value2->name = 'Sarah Millas';
        $value2->age = 24;

        $this->assertNotSame($unknow->get()->name, $value2->name);
        $this->assertNotSame($unknow->get()->age, $value2->age);

        $unknow3 = $unknow;
        $value3 = $unknow3->get();
        $value3->name = 'Johnatan Pierce';
        $value3->age = 32;

        $this->assertSame('Johnatan Pierce', $unknow->get()->name);
        $this->assertSame(32, $unknow->get()->age);
    }

    public function test_unknown_cast_method()
    {
        $variable = Unknown::new('12');
        $this->assertNotSame(12, $variable->get());
        $this->assertSame(12, $variable->toInt());

        $variable = Unknown::new('.457');
        $this->assertSame(.46, $variable->toFloat(2));

        $variable = Unknown::new(new ArrayIterator([1, 2, 3, 4, 5]));
        $this->assertSame([1, 2, 3, 4, 5], $variable->toArray());

        $variable = Unknown::new(4.5);
        $this->assertSame('4.5', $variable->toString());
    }
}
