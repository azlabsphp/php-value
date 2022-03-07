<?php

namespace Drewlabs\Immutable\Tests;

use Drewlabs\Immutable\Accessible;
use Drewlabs\Immutable\Exceptions\ImmutableValueException;
use Drewlabs\Immutable\Tests\Stubs\FileLogger;
use Drewlabs\Immutable\Tests\Stubs\Message;
use Drewlabs\Immutable\Tests\Stubs\ValueStub;
use Drewlabs\Immutable\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{

    public function testValueObjectCopyWithMethod()
    {
        $message = new Message(
            [
                'From' => 'xxx-xxx-xxx',
                'To' => 'yyy-yyy-yyy',
                'Logger' => new FileLogger(),
            ]
        );
        $message_z = $message->copyWith([
            'From' => 'zzz-zzz-zzz',
        ]);
        $message_z->Logger->updateMutable();
        $this->assertTrue('xxx-xxx-xxx' === $message->from);
        $this->assertNotSame($message_z->from, $message->from);
    }

    public function testValueObjectImmutableSetterMethod()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->expectException(ImmutableValueException::class);
        $message->From = 'zzz-zzz-yyy';

        $this->assertTrue(true);
    }

    public function testValueObjectImmutableUnsetMethodThrowsException()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->expectException(ImmutableValueException::class);
        unset($message->From);
    }

    public function testValueObjectImmutableOffsetSetMethodThrowsException()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue($message->offsetExists('from'));
        $this->assertSame($message->offsetGet('from'), 'xxx-xxx-xxx', 'Expect from property to equals xxx-xxx-xxx');
        $this->expectException(ImmutableValueException::class);
        $message->offsetSet('From', 'YYY-YYY-YY');
    }

    public function testJsonSerializeMethod()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue(drewlabs_core_strings_contains($message->jsonSerialize()['From'], 'xxx'), 'Expect the from property to contains xxx');
    }

    public function testNonAssocValueObject()
    {
        $value = new ValueStub([
            'name' => 'Azandrew Sidoine',
            'address' => 'KEGUE, LOME - TOGO',
        ]);
        $this->assertTrue(drewlabs_core_strings_contains($value->name, 'Azandrew'), 'Expect the value name property to be a string that contains Azandrew');
    }

    public function testPropertiesGetterMethod()
    {
        $value = new ValueStub([
            'name' => 'Azandrew Sidoine',
            'address' => 'KEGUE, LOME - TOGO',
        ]);

        $this->assertSame($value->name, 'Azandrew Sidoine', 'Expect name property value to equals Azandrew Sidoine');
    }

    public function testFromStdClassMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();
        $message = (new Message())->fromStdClass($object);
        $this->assertSame($message->From, 'xxx-xxx-xxx', 'Expect from property value to equals xxx-xxx-xxx');
    }

    public function testAttributesToArrayMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();
        $message = (new Message())->fromStdClass($object);
        $this->assertIsArray($message->attributesToArray(), 'Expect attributesToArray() method to return an array');
        $this->assertSame($message['from'], 'xxx-xxx-xxx');
    }

    public function testToStringMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();

        $address = new \stdClass();
        $address->email = 'test@example.com';
        $geolocation = new \stdClass();
        $geolocation->lat = '6.09834355';
        $geolocation->long = '4.8947352';
        $address->geolocation = $geolocation;
        $object->Address = $address;
        $message = (new Message())->fromStdClass($object);
        $this->assertIsString((string) $message, 'Expect object to be stringeable');
    }

    public function testValueObjectGetIteratorMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';

        $address = new \stdClass();
        $address->email = 'test@example.com';
        $geolocation = new Accessible();
        $geolocation->lat = '6.09834355';
        $geolocation->long = '4.8947352';
        $address->geolocation = $geolocation;
        $object->Address = $address;
        $message = (new Message())->fromStdClass($object);
        $this->assertSame('test@example.com', $message->getAttribute('address.email'), 'Expect email to equals test@example.com');
    }

    public function test_create_value_function()
    {
        /**
         * @property name
         * @property $lastname
         */
        $value =  CreateValue([
            'name',
            'lastname'
        ]);
        $this->assertInstanceOf(Value::class, $value);
        // Call copy method to create a copy of the object
        $value = $value->copy([
            'name' => 'Azandrew',
            'lastname' => 'Sidoine',
            'age' => 29
        ]);
        $this->assertNull($value->age);
        $this->assertEquals($value->name, 'Azandrew');
    }
}
