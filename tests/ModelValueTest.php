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

use Drewlabs\Immutable\Tests\Stubs\TestModel;
use Drewlabs\Immutable\Tests\Stubs\TestModelValue;
use PHPUnit\Framework\TestCase;

class ModelValueTest extends TestCase
{
    public function test_get_label_property()
    {
        $model = new TestModelValue(new TestModel());
        $this->assertSame($model->label, 'HELLO WORLD!', 'Expect label attribute getter to return HELLO WORLD!');
    }

    public function test_set_comments_property()
    {
        $model = new TestModelValue(new TestModel());
        $model = $model->copy([
            'comments' => ['Github issues issues']
        ]);
        $this->assertSame($model->comments[0]['content'], 'Github issues issues');
    }

    public function test_get_title_property()
    {
        $model = new TestModelValue(new TestModel());
        $this->assertSame($model->title, 'Welcome to it world');
    }

    public function test_call_model_methods()
    {
        $value = new TestModelValue(new TestModel());
        $this->assertEquals($value->getKey(), 1);
        $this->assertEquals($value->getPrimaryKey(), 'id');

    }
}
