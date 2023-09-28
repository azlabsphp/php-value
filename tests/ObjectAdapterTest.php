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

use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Accessible;
use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Exceptions\ImmutableValueException;

use function Drewlabs\PHPValue\Functions\CreateAdapter;

use Drewlabs\PHPValue\Tests\Stubs\FileLogger;
use Drewlabs\PHPValue\Tests\Stubs\Message;
use Drewlabs\PHPValue\Tests\Stubs\User;
use Drewlabs\PHPValue\Tests\Stubs\UserDetails;
use Drewlabs\PHPValue\Tests\Stubs\ValueStub;

use PHPUnit\Framework\TestCase;

class ObjectAdapterTest extends TestCase
{
    public function test_Value_Object_Copy_Method()
    {
        $message = Message::new(
            [
                'From' => 'xxx-xxx-xxx',
                'To' => 'yyy-yyy-yyy',
                'Logger' => new FileLogger(),
            ]
        );
        $message_z = $message->copy([
            'From' => 'zzz-zzz-zzz',
        ]);
        $message_z->Logger->updateMutable();
        $this->assertTrue('xxx-xxx-xxx' === $message->from);
        $this->assertNotSame($message_z->from, $message->from);
        $this->assertSame('zzz-zzz-zzz', $message_z->From);
    }

    public function testValueObjectImmutableSetterMethod()
    {
        $message = Message::new([
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
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->expectException(ImmutableValueException::class);
        unset($message->From);
    }

    public function testValueObjectImmutableOffsetSetMethodThrowsException()
    {
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue($message->offsetExists('from'));
        $this->assertSame($message->offsetGet('from'), 'xxx-xxx-xxx', 'Expect from property to equals xxx-xxx-xxx');
        $this->expectException(ImmutableValueException::class);
        $message->offsetSet('From', 'YYY-YYY-YY');
    }

    public function testSerializeMethod()
    {
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue(Str::contains($message->jsonSerialize()['from'], 'xxx'), 'Expect the from property to contains xxx');
    }

    public function testNonAssocValueObject()
    {
        $value = ValueStub::new([
            'name' => 'Azandrew Sidoine',
            'address' => 'KEGUE, LOME - TOGO',
        ]);
        $this->assertTrue(Str::contains($value->name, 'Azandrew'), 'Expect the value name property to be a string that contains Azandrew');
    }

    public function testPropertiesGetterMethod()
    {
        $value = ValueStub::new([
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
        // $object->Logger = new FileLogger();
        $message = Message::new()->fromObject($object);
        $this->assertSame($message->From, 'xxx-xxx-xxx', 'Expect from property value to equals xxx-xxx-xxx');
    }

    public function testAttributesToArrayMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();
        $message = Message::new()->fromObject($object);
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
        $message = Message::new()->fromObject($object);
        $this->assertIsString((string) $message, 'Expect object to be stringeable');
    }

    public function test_Value_Object_Get_Iterator_Method()
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
        $message = Message::new()->fromObject($object);
        $this->assertSame('test@example.com', $message->getAttribute('Address.email'), 'Expect email to equals test@example.com');
    }

    public function test_create_value_function()
    {
        /**
         * @property name
         * @property $lastname
         */
        $value = CreateAdapter([
            'name',
            'lastname',
        ]);
        $this->assertInstanceOf(ValueInterface::class, $value);
        // Call copy method to create a copy of the object
        $value = $value->copy([
            'name' => 'Azandrew',
            'lastname' => 'Sidoine',
            'age' => 29,
        ]);
        $this->assertNull($value->age);
        $this->assertSame($value->name, 'Azandrew');
    }

    public function test_cast_implementation_for_class()
    {
        $user = new User([
            'username' => 'USER-939',
            'password' => 'secret',
            'isVerified' => 0,
            'details' => [
                'firstname' => 'AZANDREW',
                'lastname' => 'SIDOINE',
                'emails' => 'azandrewdevelopper@gmail.com',
            ],
            'roles' => [
                'create-accounts',
            ],
        ]);
        $this->assertFalse($user->isVerified);
        $this->assertInstanceOf(UserDetails::class, $user->details);
        $this->assertIsArray($user->details->emails);
        $this->assertSame($user->details->emails[0], 'azandrewdevelopper@gmail.com');
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_value_object_add_properties_method()
    {
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
        ]);
        $message = $message->addProperties(['address', 'ratings']);
        $message = $message->copy(['ratings' => 4.4]);
        $this->assertSame(4.4, $message->ratings);
    }

    public function test_object_adapter_macroable_trait()
    {
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
        ]);

        $message = $message->bind('sendMessage', function (string $_) {
            return true;
        });

        $this->assertTrue($message->sendMessage('Hello World!'));
    }

    public function test_object_adapter_macroable_bind_override_method_if_it_does_exists()
    {
        $message = Message::new([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
        ]);

        $message = $message->bind('sendMessage', function (string $_) {

            return true;
        });

        $this->assertTrue($message->sendMessage('Hello World!'));

        // Bind to the same method a new callback
        $message = $message->bind('sendMessage', function (string $_) {
            return false;
        });

        // Expect the new callback to return false as result
        $this->assertFalse($message->sendMessage('Hello World!'));
    }

    public function test_get_object_adapter_to_array_returns_an_array_with_appended_properties()
    {
        $user = new User([
            'username' => 'USER-939',
            'password' => 'secret',
            'isVerified' => 0,
            'details' => [],
            'email' => 'azandrewdevelopper@gmail.com',
            'roles' => [],
        ]);

        $this->assertSame('azandrewdevelopper@gmail.com', $user->toArray()['email']);
    }
}
