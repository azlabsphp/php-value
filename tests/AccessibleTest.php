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

use Drewlabs\PHPValue\Accessible;
use PHPUnit\Framework\TestCase;

class AccessibleTest extends TestCase
{
    public function testIsEmptyMethod()
    {
        $this->assertTrue((new Accessible())->isEmpty(), 'Expect new PhpStdClass to be empty');
    }

    public function testIssetMethod()
    {
        $p = new Accessible();
        $p->value = 'Hello';
        $this->assertTrue(isset($p->value), 'Expect the isset call on php std class object property to return true');

        unset($p->value);
        $this->assertTrue(!isset($p->value), 'Expect the value object to not be set after unset() call');
    }

    public function testEnumerateMethod()
    {
        $p = new Accessible();
        $p->firstname = 'Sidoine';
        $p->lastName = 'Azandrew';
        iterator_to_array(
            $p->each(static function ($key, $value) {
            })
        );
        $this->assertTrue(true);
    }
}
