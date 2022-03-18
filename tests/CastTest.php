<?php


namespace Drewlabs\Support\Tests\Unit;

use DateTime;
use DateTimeImmutable;
use Drewlabs\Immutable\Cast;
use Drewlabs\Immutable\Tests\Stubs\FileLogger;
use Drewlabs\Immutable\Tests\Stubs\Message;
use Drewlabs\Immutable\Tests\Stubs\TestModel;
use Drewlabs\Immutable\Tests\Stubs\ValueStub;
use PHPUnit\Framework\TestCase;

class CastTest extends TestCase
{

    public function test__invoke__method()
    {
        $cast = new Cast();
        $mail_cmd = new class extends \stdClass {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";      
            }
        };
        $this->assertIsArray($cast->__invoke('array', '[1,2,3,4]'));
        $this->assertEquals(true, $cast->__invoke('bool', 1));
        $this->assertEquals(false, $cast->__invoke('boolean', null));
        $this->assertEquals("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->__invoke('string', $mail_cmd));
        $this->assertIsArray($cast->__invoke('json', '[1,2,3,4]'));
        $this->assertEquals(3, $cast->__invoke('int', 3.144142));
        $this->assertEquals(intval('0b11', 2), $cast->__invoke('int:2', '0b11')); // Test if int values is parse in the specified base
        $this->assertEquals(3.144142, $cast->__invoke('float', '3.144142'));
        $this->assertEquals(3.144142, $cast->__invoke('real', '3.144142'));
        $this->assertEquals((new DateTime)->setTime(0, 0), $cast->__invoke('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTime::class, $cast->__invoke('datetime', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->setTime(0, 0), $cast->__invoke('immutable_date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTimeImmutable::class, $cast->__invoke('immutable_datetime', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->getTimestamp(), $cast->__invoke('timestamp', date('Y-m-d H:i:s')));
    }

    public function test__call__method()
    {
        $cast = new Cast();
        $mail_cmd = new class extends \stdClass {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";      
            }
        };
        $this->assertIsArray($cast->call('array', '[1,2,3,4]'));
        $this->assertEquals(true, $cast->call('bool', 1));
        $this->assertEquals(false, $cast->call('boolean', null));
        $this->assertEquals("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->call('string', $mail_cmd));
        $this->assertIsArray($cast->call('json', '[1,2,3,4]'));
        $this->assertEquals(3, $cast->call('int', 3.144142));
        $this->assertEquals(3.144142, $cast->call('float', '3.144142'));
        $this->assertEquals(3.144142, $cast->call('real', '3.144142'));
        $this->assertEquals((new DateTime)->setTime(0, 0), $cast->call('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTime::class, $cast->call('datetime', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->setTime(0, 0), $cast->call('immutable_date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTimeImmutable::class, $cast->call('immutable_datetime', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->getTimestamp(), $cast->call('timestamp', date('Y-m-d H:i:s')));
    }

    public function test_get_cast_type_function_method()
    {
        $cast = new Cast();
        $this->assertInstanceOf(\Closure::class, $cast->getCastType('decimal')[1]);
        $this->assertNull($cast->getCastType(TestModel::class));
        $this->assertInstanceOf(\Closure::class, $cast->getCastType('decimal:8')[1]);
        $this->assertInstanceOf(\Closure::class, $cast->getCastType('datetime:Y-m-d H:i:s')[1]);
    }

    public function test_is_enum_castable_method()
    {
        $this->assertTrue(true);
    }

    public function test_is_class_castable_method()
    {
        $cast = new Cast();
        $this->assertFalse($cast->isClassCastable(null, 'message'));
        $cast->setCasts([
            'message' => Message::class,
            'test' => TestModel::class . ":hello"
        ]);
        $this->assertTrue($cast->isClassCastable(null, 'message'));
        $this->assertTrue($cast->isClassCastable(null, 'test'));
    }

    public function test_get_cast_class_property_method()
    {
        $cast = new Cast(new ValueStub([
            'message' => [
                'To' => 'az@sedana.com',
                'From' => 'contact@myriad.com',
                'Logger' => new FileLogger,
                'Address' => '234 Bvd FELIX HOUPHET, TREIJVILLE',
            ]
        ]));
        $result = $cast->getClassCastableProperty('message', null);
        $this->assertTrue($result instanceof Message);
        $this->assertTrue(($result->To === $result->to) && ($result->to === 'az@sedana.com'));
    }

}