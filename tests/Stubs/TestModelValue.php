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

namespace Drewlabs\Immutable\Tests\Stubs;

use Drewlabs\Immutable\ModelValue;

class TestModelValue extends ModelValue
{

    protected $___properties = [
        'label',
        'comments',
        'title',
    ];

    public function getLabelAttribute()
    {
        return strtoupper($this->getRawAttribute('label'));
    }

    protected function setCommentsAttribute(?array $comments)
    {
        $this->setRawAttribute(
            'comments',
            array_map(
                static function ($comment) {
                    return [
                        'content' => is_array($comment) ? $comment['description'] : $comment,
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
