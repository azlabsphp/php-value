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

use Drewlabs\PHPValue\Utils\SanitizeCustomProperties;
use PHPUnit\Framework\TestCase;

class SanitizeCustomPropertiesTest extends TestCase
{
    public function test_sanitize_custom_properties__invoke()
    {
        $result = (new SanitizeCustomProperties(sort: true))(['product.type', '*', 'name', 12, 'ratings']);
        $this->assertSame(['name', 'product.type', 'ratings'], $result);
    }

    public function test_sanitize_custom_properties_call()
    {
        $result = (new SanitizeCustomProperties(sort: true))->call(['product.type', 'name', 12, 'ratings']);
        $this->assertSame(['name', 'product.type', 'ratings'], $result);
    }

    public function test_sanitize_custom_properties_revomes_duplicates()
    {
        $result = (new SanitizeCustomProperties(sort: true))->call(['product.type', '*', 'name', 'product.type', 'ratings', 'name']);
        $this->assertSame(['name', 'product.type', 'ratings'], $result);
    }
}
