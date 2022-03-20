# Immutable

Library propose a PHP value object that provide interfaces for serialization and deserialization from PHP [array] or [\stdClass].

Note: Implementation of the value object tries to enforce immutability of object by offering interfaces for copying properties into a new value instance instead of modifying state.

Value object implementation tries to leverage the dynamic aspect of the PHP Programming language to provide flexible object structure, while enforcing the property define/declaration at initialization.

## Installation

Recommended way to install the library is by using composer package manager. As it's a github package an is under development you should add package and repository reference:

```json
// composer.json
{
    // ...
    "require": {
        "drewlabs/php-value": "^0.1.10"
    },
    // Adding repository
    
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:liksoft/drewlabs-php-value.git"
        }
    ]
}
```

After you modify your composer.json you simply run:

> composer update

## Usage

The library offer two interface for creating an object. 

Your can use OOP implementation you can extends the abstract [Drewlabs\PHPValue\Value] class:

- Object oriented implementation

```php
use Drewlabs\PHPValue\Value;

class ValueStub extends Value
{
    protected $__PROPERTIES__ = [
        'name',
        'address',
    ];
}

// Creating instance
$value = new ValueStub([
    'name' => 'Azandrew',
    'address' => '288 Avenue Pia, Lome'
]);
```

Or using the [Drewlabs\PHPValue\Functions\CreateValue] function.

- Functional interface

```php
// Imports
use function Drewlabs\PHPValue\Functions\CreateValue;

$value =  CreateValue([
    // dynamic properties
    'name',
    'address'
]);
```

Using both ways, you create an instance of [\Drewlabs\PHPValue\Value] class.

- Creating a copy of the object

```php
// Imports
use function Drewlabs\PHPValue\Functions\CreateValue;
$value =  CreateValue([
    // dynamic properties
    'name',
    'address'
]);

// This tries to create a deep copy of the object
$value1 = $value->copy([
    'name' => 'Sidoine Azandrew'
]);
```

- Getting property of the object

The value object is Array accessible meaning we can user [] operator to acces object properties. It also overrides magic [__get] method for properties accesibility and offers a [getAttribute()] method that query for a property on the object

```php
// Imports
use function Drewlabs\PHPValue\Functions\CreateValue;
$value =  CreateValue([
    // dynamic properties
    'name',
    'address'
]);

$result = $value['name']; 
// Same as
$result = $value->name;
// Same as
$result = $value->getAttribute('name');
```
