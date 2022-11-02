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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\PHPValue\Cast;
use Drewlabs\PHPValue\Tests\Stubs\FileLogger;
use Drewlabs\PHPValue\Tests\Stubs\LikeStub;
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
            't_updated_at' => 'timestamp',
        ];
    }

    public function test__invoke__method()
    {
        $cast = (new Cast())->setCasts($this->createCasts());
        $mail_cmd = new class() extends \stdClass {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";
            }
        };
        $this->assertIsArray($cast->__invoke('items', '[{"name": "Clean Soap"}, {"name": "Knife"}]'));
        $this->assertTrue($cast->__invoke('paid', 1));
        $this->assertFalse($cast->__invoke('paid', 0));
        $this->assertSame("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->__invoke('mail', $mail_cmd));
        $this->assertSame(3, $cast->__invoke('total', 3.144142));
        $this->assertSame('3.14', $cast->__invoke('amount', '3.144142'));
        $this->assertSame(3.144142, $cast->__invoke('pi', '3.144142'));
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')), $cast->__invoke('created_at', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(\DateTime::class, $cast->__invoke('created_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new \DateTimeImmutable())->setTime(0, 0), $cast->__invoke('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(\DateTimeImmutable::class, $cast->__invoke('updated_at', date('Y-m-d H:i:s')));
        $this->assertSame((new \DateTimeImmutable())->getTimestamp(), $cast->__invoke('t_updated_at', date('Y-m-d H:i:s')));
    }

    public function test__call__method()
    {
        $cast = (new Cast())->setCasts($this->createCasts());
        $mail_cmd = new class() extends \stdClass {
            public function __toString()
            {
                return "sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'";
            }
        };
        $this->assertIsArray($cast->call('items', '[{"name": "Clean Soap"}, {"name": "Knife"}]'));
        $this->assertTrue($cast->call('paid', 1));
        $this->assertFalse($cast->call('paid', 0));
        $this->assertSame("sendmail:dns://smtp.liksoft.tg:587 --to azandrewdevelopper@gmail.com --subject='Hello World!'", $cast->call('mail', $mail_cmd));
        $this->assertSame(3, $cast->call('total', 3.144142));
        $this->assertSame('3.14', $cast->__invoke('amount', '3.144142'));
        $this->assertSame(3.144142, $cast->call('pi', '3.144142'));
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')), $cast->call('created_at', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(\DateTime::class, $cast->call('created_at', date('Y-m-d H:i:s')));
        $this->assertEquals((new \DateTimeImmutable())->setTime(0, 0), $cast->call('date', date('Y-m-d H:i:s')));
        $this->assertInstanceOf(\DateTimeImmutable::class, $cast->call('updated_at', date('Y-m-d H:i:s')));
        $this->assertSame((new \DateTimeImmutable())->getTimestamp(), $cast->call('t_updated_at', date('Y-m-d H:i:s')));
    }

    public function test_get_cast_type_function_method()
    {
        $cast = (new Cast())->setCasts($this->createCasts());
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
            'test' => TestModel::class.':hello',
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
                'Logger' => new FileLogger(),
                'Address' => '234 Bvd FELIX HOUPHET, TREIJVILLE',
            ],
        ]));
        $result = $cast->getClassCastableProperty('message', null);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertTrue(($result->To === $result->to) && ('az@sedana.com' === $result->to));
    }

    public function test_get_cast_castable_collection_properties()
    {
        $like1 = new \stdClass();
        $like1->count = 2;
        $like1->user = 'Philip Casper';

        $like2 = new \stdClass();
        $like2->count = 12;
        $like2->user = 'Lauren Flora';

        $cast = new Cast(new ValueStub([
            'likes' => [$like1, $like2],
            'errors' => [
                'Server Error',
                'Not Found HTTP Error',
            ],
        ]));

        $result = $cast->getClassCastableProperty('likes', null);
        $this->assertInstanceOf(LikeStub::class, $result[0]);
        $this->assertSame(2, $result[0]->getCount());

        $result = $cast->getClassCastableProperty('errors', null);
        $this->assertSame('Not Found HTTP Error', iterator_to_array($result)[1]->getMessage());
    }
}
