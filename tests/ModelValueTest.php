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

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Tests\Stubs\TestModel;
use Drewlabs\PHPValue\Tests\Stubs\TestModelValue;
use Drewlabs\PHPValue\Tests\Stubs\TestResolvableValueModel;
use PHPUnit\Framework\TestCase;

class ModelValueTest extends TestCase
{
    public function test_get_label_property()
    {
        $model = TestModelValue::new()->adapt(new TestModel());
        $this->assertSame($model->label, 'HELLO WORLD!', 'Expect label attribute getter to return HELLO WORLD!');
    }

    public function test_set_comments_property()
    {
        $model = new TestModelValue(new TestModel());
        $model = $model->copy(['comments' => ['Github issues issues']]);
        $this->assertSame($model->Comments[0]['content'], 'Github issues issues');
    }

    public function test_get_title_property()
    {
        $model = new TestModelValue(new TestModel());
        $this->assertSame($model->title, 'Welcome to IT World');
    }

    public function test_call_model_methods()
    {
        $value = new TestModelValue(new TestModel());
        $this->assertSame($value->getKey(), 1);
        $this->assertSame($value->getPrimaryKey(), 'id');
    }

    public function test_model_value_to_array()
    {
        $object = new TestModelValue(new TestModel());
        $this->assertInstanceOf(ValueInterface::class, $object->person);
    }

    public function test_resolve_model_fallback_call()
    {
        $value = new TestResolvableValueModel();
        $this->assertTrue(1 === $value->getKey());
    }
}
