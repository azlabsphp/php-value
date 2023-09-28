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

use Drewlabs\PHPValue\ObjectProxy;
use PHPUnit\Framework\TestCase;
use stdClass;

class ObjectProxyTest extends TestCase
{
    public function test_object_proxy_isset()
    {
        $object = new \stdClass;
        $object->value = 'My Value';

        $proxy = new ObjectProxy($object);
        $this->assertTrue(isset($proxy->value), 'Expect the isset call on php std class object property to return true');

        unset($proxy->value);
        $this->assertTrue(!isset($proxy->value), 'Expect the value object to not be set after unset() call');
    }


    public function test_get_property_value()
    {
        $person = new stdClass;
        $person->name = 'John Doe';
        $person->age = 32;

        $address = new stdClass;
        $address->email = 'johndoe@example.com';
        $address->house = '21 Lincoln Street';

        $person->address = $address;
        $proxy = new ObjectProxy($person);
        
        $this->assertEquals('johndoe@example.com', $proxy->getPropertyValue('address.email'));
        $this->assertEquals('21 Lincoln Street', $proxy->getPropertyValue('address.house'));
        $this->assertEquals(32, $proxy->getPropertyValue('age'));
    }


    public function test_object_proxy_set_property_value()
    {
        $person = new stdClass;
        $person->name = 'John Doe';
        $person->age = 32;

        $address = new stdClass;
        $address->email = 'johndoe@example.com';
        $address->house = '21 Lincoln Street';

        $person->address = $address;
        $proxy = new ObjectProxy($person);
        
        $this->assertEquals(null, $proxy->getPropertyValue('address.phone'));

        $addressProxy = new ObjectProxy($address);
        $addressProxy->setPropertyValue('phone', '22898723456');

        $this->assertEquals('22898723456', $proxy->getPropertyValue('address.phone'));
    }

    public function test_object_proxy_property_exists()
    {
        // Initialiaze
        $person = new stdClass;
        $person->name = 'John Doe';
        $person->age = 32;

        // Act
        $proxy = new ObjectProxy($person);

        // Assert
        $this->assertTrue($proxy->propertyExists('age'));
        $this->assertFalse($proxy->propertyExists('address'));

    }
}
