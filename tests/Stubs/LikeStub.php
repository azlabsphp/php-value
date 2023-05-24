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

class LikeStub
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var string|int
     */
    private $user;

    /**
     * Creates an instance of {@see LikeSub} class.
     *
     * @param array|object $attributes
     *
     * @return void
     */
    public function __construct($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Returns the likes count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Returns the user attached to the like.
     *
     * @return string|int
     */
    public function getUser()
    {
        return $this->user;
    }
}
