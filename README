# PHP Value

The library provides utility objects for PHP application and projects.

## Usage

The library offer two interface for creating an object.

Your can use OOP implementation you can extends the abstract [Drewlabs\PHPValue\Value] class:

- Object oriented implementation

```php
use Drewlabs\PHPValue\ObjectAdapter;

class ValueStub extends ObjectAdapter
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
use function Drewlabs\PHPValue\Functions\CreateAdapter;

$value =  CreateAdapter([
    // dynamic properties
    'name',
    'address'
]);
```

Using both ways, you create an instance of [\Drewlabs\PHPValue\Value] class.

- Creating a copy of the object

```php
// Imports
use function Drewlabs\PHPValue\Functions\CreateAdapter;
$value =  CreateAdapter([
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
use function Drewlabs\PHPValue\Functions\CreateAdapter;
$value =  CreateAdapter([
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
