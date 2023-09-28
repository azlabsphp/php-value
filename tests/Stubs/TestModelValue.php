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

namespace Drewlabs\PHPValue\Tests\Stubs;

use Drewlabs\PHPValue\Contracts\ValueInterface;
use Drewlabs\PHPValue\Traits\ObjectAdapter;

class TestModelValue implements ValueInterface
{
    use ObjectAdapter;

    private const __PROPERTIES__ = [
        'label' => 'label',
        'Comments' => 'comments',
        'title' => 'title',
    ];

    /**
     * Creates class instance.
     *
     * @param array|Adaptable|Accessible $adaptable
     */
    public function __construct($adaptable = null)
    {
        // code...
        $this->bootInstance(static::__PROPERTIES__, $adaptable);
    }

    public function getCasts()
    {
        return [
            'label' => static function ($value) {
                return null !== $value ? strtoupper($value) : $value;
            },
            'person' => 'value:'.TestModelRelation1Value::class,
        ];
    }

    /**
     * returns the list of owned properties.
     *
     * @return string[]
     */
    public function propertiesDefnition()
    {
        // code...
        return $this->__PROPERTIES__ ?? [];
    }

    /**
     * set properties cast definitions.
     *
     * @return string[]
     */
    public function setCasts(array $values)
    {
        // code...
        $this->__CASTS__ = $values ?? $this->__CASTS__ ?? [];

        return $this;
    }

    /**
     * returns the list of hidden properties.
     *
     * @return string[]
     */
    public function getHidden()
    {
        // code...
        return $this->__HIDDEN__ ?? [];
    }

    /**
     * set properties hidden properties.
     *
     * @return string[]
     */
    public function setHidden(array $values)
    {
        // code...
        $this->__HIDDEN__ = $values;

        return $this;
    }

    protected function setCommentsAttribute(?array $comments)
    {
        $this->setRawAttribute(
            'comments',
            array_map(
                static function ($comment) {
                    return [
                        'content' => \is_array($comment) ? $comment['content'] : $comment,
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
