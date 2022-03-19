<?php


namespace Drewlabs\Support\Tests\Unit;

use DateTime;
use DateTimeImmutable;
use Drewlabs\PHPValue\Cast;
use Drewlabs\PHPValue\Tests\Stubs\FileLogger;
use Drewlabs\PHPValue\Tests\Stubs\Message;
use Drewlabs\PHPValue\Tests\Stubs\TestModel;
use Drewlabs\PHPValue\Tests\Stubs\ValueStub;
use PHPUnit\Framework\TestCase;

class CastTest extends TestCase
{

    public function createCasts()
    {
        return [
            'amount' => 'decimal:2',
            'total' => 'int',
            'paid' => 'bool',
            'date' => 'immutable_date',
            'created_at' => 'datetime',
            'updated_at' => 'immutable_datetime',
            'items' => 'array',
            'mail' => 'string',
            'pi' => 'real',
            't_updated_at' => 'timestamp'
        ];
    }

    public function test__invoke__method()
    {
        $cast = (new Cast())->setCasts($this->createCasts());
        $mail_cmd = new class extends \stdClass
        {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";
            }
        };
        $this->assertIsArray($cast->__invoke('items', '[{"name": "Clean Soap"}, {"name": "Knife"}]'));
        $this->assertEquals(true, $cast->__invoke('paid', 1));
        $this->assertEquals(false, $cast->__invoke('paid', 0));
        $this->assertEquals("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->__invoke('string', $mail_cmd));
        $this->assertEquals(3, $cast->__invoke('total', 3.144142));
        $this->assertEquals('3.14', $cast->__invoke('amount', '3.144142'));
        $this->assertEquals(3.144142, $cast->__invoke('pi', '3.144142'));
        $this->assertEquals(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')), $cast->__invoke('created_at', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTime::class, $cast->__invoke('created_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->setTime(0, 0), $cast->__invoke('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTimeImmutable::class, $cast->__invoke('updated_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->getTimestamp(), $cast->__invoke('t_updated_at', date('Y-m-d H:i:s')));
    }

    public function test__call__method()
    {
        $cast = (new Cast())->setCasts($this->createCasts());
        $mail_cmd = new class extends \stdClass
        {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";
            }
        };
        $this->assertIsArray($cast->call('items', '[{"name": "Clean Soap"}, {"name": "Knife"}]'));
        $this->assertEquals(true, $cast->call('paid', 1));
        $this->assertEquals(false, $cast->call('paid', 0));
        $this->assertEquals("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->call('string', $mail_cmd));
        $this->assertEquals(3, $cast->call('total', 3.144142));
        $this->assertEquals('3.14', $cast->__invoke('amount', '3.144142'));
        $this->assertEquals(3.144142, $cast->call('pi', '3.144142'));
        $this->assertEquals(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')), $cast->call('created_at', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTime::class, $cast->call('created_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->setTime(0, 0), $cast->call('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(DateTimeImmutable::class, $cast->call('updated_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new DateTimeImmutable)->getTimestamp(), $cast->call('t_updated_at', date('Y-m-d H:i:s')));
    }

    public function test_get_cast_type_function_method()
    {
        $cast = (new Cast)->setCasts($this->createCasts());
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
        $this->assertFalse($cast->isClassCastable('message'));
        $cast->setCasts([
            'message' => Message::class,
            'test' => TestModel::class . ":hello"
        ]);
        $this->assertTrue($cast->isClassCastable('message'));
        $this->assertTrue($cast->isClassCastable('test'));
    }

    public function test_get_cast_castable_properties()
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
