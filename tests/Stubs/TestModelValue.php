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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\ModelAwareValue;

class TestModelValue implements ValueInterface
{

    use ModelAwareValue;

    protected $__PROPERTIES__ = [
        'label',
        'comments',
        'title',
    ];

    public function getCasts()
    {
        return [
            'label' => function ($value) {
                return null !== $value ? strtoupper($value) : $value;
            },
            'person' => 'value:' . TestModelRelation1Value::class
        ];
    }

    protected function setCommentsAttribute(?array $comments)
    {
        $this->setRawAttribute(
            'comments',
            array_map(
                static function ($comment) {
                    return [
                        'content' => is_array($comment) ? $comment['content'] : $comment,
                    ];
                },
                $comments ?? []
            )
        );
    }

    protected function setTitleAttribute(?string $value)
    {
        $this->setRawAttribute('title', $value ? ucfirst(strtolower($value)) : $value);
    }
}
