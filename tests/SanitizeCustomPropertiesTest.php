<?php

use Drewlabs\PHPValue\Utils\SanitizeCustomProperties;
use PHPUnit\Framework\TestCase;

class SanitizeCustomPropertiesTest extends TestCase
{
    public function test_sanitize_custom_properties__invoke()
    {
        $result = (new SanitizeCustomProperties(sort: true))(['product.type', '*', 'name', 12, 'ratings']);
        $this->assertEquals(['name', 'product.type', 'ratings'], $result);
    }

    public function test_sanitize_custom_properties_call()
    {
        $result = (new SanitizeCustomProperties(sort: true))->call(['product.type', 'name', 12, 'ratings']);
        $this->assertEquals(['name', 'product.type', 'ratings'], $result);
    }

    public function test_sanitize_custom_properties_revomes_duplicates()
    {
        $result = (new SanitizeCustomProperties(sort: true))->call(['product.type', '*', 'name', 'product.type', 'ratings', 'name']);
        $this->assertEquals(['name', 'product.type', 'ratings'], $result);

    }
}
