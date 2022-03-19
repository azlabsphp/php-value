<?php

namespace Drewlabs\PHPValue;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\PHPValue\Contracts\CastPropertyInterface;
use Drewlabs\PHPValue\Contracts\CastsInboundProperties;
use Drewlabs\PHPValue\Exceptions\InvalidCastException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Drewlabs\PHPValue\Contracts\CastsAware;
use Drewlabs\PHPValue\Exceptions\JsonEncodingException;


/**
 * 
 * @package Drewlabs\PHPValue
 */
class Cast
{
    const NAMESPACE = __NAMESPACE__ . '\\Casts';

    /**
     * 
     * @var array
     */
    private $map = [];

    /**
     * 
     * @var array
     */
    private $casts = [];

    /**
     * 
     * @var array
     */
    private $castersCache = [];

    /**
     * 
     * @var CastsAware
     */
    private $castsAware;

    public function __construct(CastsAware $castsAware = null)
    {
        $this->map = $this->useDefaults();
        $this->setCastAwareInstance($castsAware);
    }

    /**
     * Merge type casting defintions into existing ones
     * 
     * ```php
     * <?php
     * import Drewlabs\BuiltType\Cast;
     * 
     * // Creates an instance of the cast aware object
     * const $object = new CastAwareObject;
     * 
     * // Build the cast manager instance from the cast aware object
     * $cast = new Cast($object);
     * 
     * // Merge type casting defintions
     * $cast = $cast->mergeCastDefinitions([
     *  'int' => function() {
     *         // code that cast and interger types
     *   }
     * ]);
     * ```
     * 
     * @param array $merge 
     * @return array 
     */
    public function mergeCastDefinitions(array $merge)
    {
        $this->map = array_merge($this->map ?? [], $merge);
        return $this;
    }

    public function __invoke(string $name, $value, ...$params)
    {
        if (null === ($this->casts[$name] ?? null)) {
            return $value;
        }
        // If the cast is a closure, invoke the closure with the provided value
        if ($this->isClosureCastable($name)) {
            return $this->getClosureCastableAttributeValue($name, $value);
        }
        if ($this->isPrimitiveCastable($name)) {
            return $this->getPrimitiveCastableAttributeValue($name, $value, ...$params);
        }
        // Is cast is a PHP enumeration
        if ($this->isEnumCastable($name)) {
            return $this->getEnumCastableAttributeValue($name, $value, ...$params);
        }
        // Is cast a PHP Class
        if ($this->isClassCastable($name)) {
            // Cast value into class type
            return $this->getClassCastableProperty($name, $value, ...$params);
        }
        return $value;
    }

    public function call(string $name, $value, ...$params)
    {
        return $this->__invoke($name, $value, ...$params);
    }


    /**
     * @description Returns the cast function matching the cast name provided by developper
     * or returns an identity function if the cast type is not found
     * 
     * @param string $key 
     * @return array<string|\Closure>  Type definition is combination of type name and \Closure func [name,\Closure]
     */
    public function getCastType(string $name)
    {
        $castTypeName = $this->getCastTypeName($name);
        return  $castTypeName && isset($this->map[$castTypeName]) ? [$castTypeName, $this->map[$castTypeName]] : null;
    }

    /**
     * Determine if the given key is cast using an enum.
     * 
     * @param Cast $cast 
     * @param array $casts 
     * @param mixed $key 
     * @return bool|void 
     */
    public function isEnumCastable($key)
    {
        if (!array_key_exists($key, $this->casts ?? [])) {
            return false;
        }
        $castType = $this->casts[$key];
        if (null !== $this->getCastType($key)) {
            return false;
        }
        if (is_string($castType) && function_exists('enum_exists') && enum_exists($castType)) {
            return true;
        }
        return false;
    }

    /**
     * Cast the given attribute to an enum.
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return mixed 
     */
    public function getEnumCastableAttributeValue($key, $value, ...$params)
    {
        if (null === $value) {
            return;
        }
        $castType = $this->casts[$key] ?? null;
        // If the $castType of the property is null we simply return the passed value
        if (null === $castType) {
            return $value;
        }
        if ($value instanceof $castType) {
            return $value;
        }
        return $castType::from($value, ...$params);
    }

    /**
     * Determine if the given key is cast using a custom class.
     * 
     * @param string $key 
     * @return bool 
     * @throws InvalidCastException 
     */
    public function isClassCastable(?string $key = null)
    {
        if (!array_key_exists($key, $this->casts ?? [])) {
            return false;
        }
        $name = $this->casts[$key] ?? null;
        // To be class castable, the cast definition must be of string type
        if (!is_string($name)) {
            return false;
        }
        $castType = $this->parseCasterClass($name);
        if (null !== $this->getCastType($castType)) {
            return false;
        }
        if (class_exists($castType)) {
            return true;
        }
        if (strpos($castType,  '\\') === false && class_exists(self::NAMESPACE . "\\" . ucfirst($castType))) {
            return true;
        }
        throw new InvalidCastException($this->castsAware, $key, $castType);
    }

    /**
     * Resolve the custom caster class for a given key.
     * 
     * @param mixed $key 
     * @return CastPropertyInterface|object 
     * @throws ReflectionException 
     */
    private function resolveCasterClass($key)
    {
        $castType = $this->casts[$key] ?? null;
        $arguments = [];
        if (is_string($castType) && strpos($castType, ':') !== false) {
            $segments = explode(':', $castType, 2);
            // Use default Class Cast namespace if required
            $castType = strpos($segments[0],  '\\') === false && !class_exists($segments[0]) && class_exists(self::NAMESPACE . "\\" . ucfirst($segments[0])) ?
                self::NAMESPACE . "\\" . ucfirst($segments[0]) :
                $segments[0];
            $arguments = explode(',', $segments[1]);
        }
        if (is_subclass_of($castType, CastPropertyInterface::class)) {
            $castType = (new ReflectionClass($castType))
                ->newInstanceWithoutConstructor()
                ->setArguments($arguments);
        }
        return is_object($castType) ? $castType : (new ReflectionClass($castType))->newInstance(...$arguments);
    }

    /**
     * Cast the given attribute using a custom cast class.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function getClassCastableProperty($key, $value, ...$params)
    {
        // Memoize previously casted value
        if (isset($this->castersCache[$key])) {
            return $this->castersCache[$key];
        } else {
            $params = array_merge([$this->castsAware], $params ?? []);
            $caster = $this->resolveCasterClass($key);
            $value = $caster instanceof CastsInboundProperties
                ? $value
                : (method_exists($caster, 'get') ? $caster->get($key, $value, ...$params) : $caster);

            if ($caster instanceof CastsInboundProperties || !is_object($value)) {
                unset($this->castersCache[$key]);
            } else {
                $this->castersCache[$key] = $value;
            }
            return $value;
        }
    }

    public function isClosureCastable($key)
    {
        return !is_string($this->casts[$key] ?? null) && is_callable($this->casts[$key] ?? null);
    }

    public function getClosureCastableAttributeValue($key, $value)
    {
        $castType = $this->casts[$key] ?? null;
        if (null === $castType) {
            return null;
        }
        return $castType($value);
    }

    public function isPrimitiveCastable($key)
    {
        $castName = $this->casts[$key] ?? null;
        return null === $this->getCastType($castName) ? false : true;
    }


    /**
     * Cast primitive attribute value
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return mixed 
     */
    public function getPrimitiveCastableAttributeValue($key, $value, ...$params)
    {
        // If Provided value is NULL simply return null as result
        if (null === $value) {
            return null;
        }
        $name = $this->casts[$key] ?? null;
        $castType = $this->getCastType($name);
        // If the return value of getCastType method is NULL return null
        if (null === $castType) {
            return null;
        }
        // We assume that the argument to the closure is specify after `:` and are seperated
        // by `,` operator
        $params = strpos($name, ':') === false ?
            [] : ((strpos($after = Str::after($castType[0] . ":", $name), ',') === false)
                ? [$after] :
                explode(',', $after));
        return $castType[1]($value, ...$params);
    }

    public function hasCast($key, $types = null)
    {
        if (array_key_exists($key, $this->casts ?? [])) {
            return $types ?
                in_array(
                    $this->getCastTypeName($key),
                    (array) $types,
                    true
                ) : true;
        }

        return false;
    }

    /**
     * Determine whether a value is Date / DateTime castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    public function isDateCastable($key)
    {
        return $this->hasCast($key, ['date', 'datetime', 'immutable_date', 'immutable_datetime']);
    }

    /**
     * Determine whether a value is JSON castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    public function isJsonCastable($key)
    {
        return $this->hasCast($key, ['array', 'json', 'object', 'collection']);
    }

    public function setCasts(array $casts)
    {
        $this->casts = $casts ?? $this->casts ?? [];
        return $this;
    }



    /**
     * Set the value of an enum castable attribute.
     *
     * @param  string  $key
     * @param  \BackedEnum  $value
     * @return void
     */
    public function computeEnumCastablePropertyValue($key, $value)
    {
        if (!isset($value)) {
            return;
        }
        $enumClass = $this->casts[$key] ?? null;
        if ($value instanceof $enumClass) {
            return $value->value;
        }
        return $enumClass::from($value)->value;
    }

    public function computeClassCastablePropertyValue($key, $value)
    {
        $caster = $this->resolveCasterClass($key);
        $valueNormalizer = function ($key, $value) {
            return Arr::isallassoc($value) ? $value : [$key => $value];
        };
        if (null === $value) {
            $result =  array_map(
                function () {
                },
                $valueNormalizer(
                    $key,
                    method_exists($caster, 'set') ?
                        $caster->set(
                            $key,
                            $value,
                            $this->castsAware
                        ) : $caster
                )
            );
        } else {
            $result =  $valueNormalizer(
                $key,
                method_exists($caster, 'set') ?
                    $caster->set(
                        $key,
                        $value,
                        $this->castsAware
                    ) : $caster
            );
        }
        if ($caster instanceof CastsInboundProperties || !is_object($value)) {
            unset($this->castersCache[$key]);
        } else {
            $this->castersCache[$key] = $value;
        }
        return $result;
    }

    /**
     * Cast the given attribute to JSON.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    public function computePropertyAsJson($key, $value)
    {
        $value = $this->asJson($value);
        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $this,
                $key,
                json_last_error_msg()
            );
        }
        return $value;
    }

    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    private function asJson($value)
    {
        return json_encode($value);
    }

    /**
     * Determine if the cast type is a custom date time cast.
     *
     * @param  string  $cast
     * @return bool
     */
    private function isParameterizedDateTimeCast($cast)
    {
        return strncmp($cast, 'date:', 5) === 0 ||
            strncmp($cast, 'datetime:', 9) === 0;
    }

    /**
     * Determine if the cast type is an immutable parameterized date time cast.
     *
     * @param  string  $cast
     * @return bool
     */
    private function isParameterizedImmutableDateTimeCast($cast)
    {
        return strncmp($cast, 'immutable_date:', 15) === 0 ||
            strncmp($cast, 'immutable_datetime:', 19) === 0;
    }

    /**
     * Determine if the cast type is a decimal cast.
     *
     * @param  string  $cast
     * @return bool
     */
    private function isDecimalCast($cast)
    {
        return strncmp($cast, 'decimal:', 8) === 0;
    }

    /**
     * Determine if the cast type is a decimal cast.
     *
     * @param  string  $cast
     * @return bool
     */
    private function isIntegerCast($cast)
    {
        return strncmp($cast, 'int:', 4) === 0 || strncmp($cast, 'integer:', 4) === 0;
    }

    private function castToPHPDate($value)
    {
        if ($value instanceof DateTimeInterface) {
            return DateTime::createFromFormat('Y-m-d H:i:s.u', $value->format('Y-m-d H:i:s.u'), $value->getTimezone());
        }
        if (is_numeric($value)) {
            return DateTime::createFromFormat(DateTime::ISO8601, date(DateTime::ISO8601, (int)$value));
        }
        try {
            // Try to parse using database connection format
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        } catch (InvalidArgumentException $e) {
            // fallback to ISO8601 standard if format does not match database connection format
            $date = DateTime::createFromFormat(DateTime::ISO8601, $value);
        }
        return $date ?: DateTime::createFromFormat(DateTime::ISO8601, $value);
    }


    private function castToPHPImmutableDate($value)
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $value->format('Y-m-d H:i:s.u'), $value->getTimezone());
        }
        if (is_numeric($value)) {
            return DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, date(DateTimeImmutable::ISO8601, (int)$value));
        }
        try {
            // Try to parse using database connection format
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        } catch (InvalidArgumentException $e) {
            // fallback to ISO8601 standard if format does not match database connection format
            $date = DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $value);
        }
        return $date ?: DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $value);
    }

    private function fromFloat($value)
    {
        switch ((string) $value) {
            case 'Infinity':
                return INF;
            case '-Infinity':
                return -INF;
            case 'NaN':
                return NAN;
            default:
                return (float) $value;
        }
    }

    /**
     * Parse the given caster class, removing any arguments.
     *
     * @param  string  $class
     * @return string
     */
    private function parseCasterClass($class)
    {
        return strpos($class, ':') === false
            ? $class
            : explode(':', $class, 2)[0];
    }

    /**
     * Create a Cast instance from user propvided cast aware class
     * 
     * @param CastsAware $castAware 
     * @param array $attributes 
     * @return self 
     */
    private function setCastAwareInstance(?CastsAware $castAware = null)
    {
        if ($castAware) {
            $this->castsAware = $castAware;
            $this->casts = $castAware->getCasts() ?? [];
        } else {
            $this->casts = [];
        }
    }

    private function useDefaults()
    {
        return [
            'string' => function ($value) {
                return (string)$value;
            },
            'array' => function ($value) {
                return json_decode($value, true);
            },
            'json' => function ($value) {
                return json_decode($value, true);
            },
            'object' => function ($value) {
                return json_decode($value, false);
            },
            'bool' => function ($value) {
                return (bool)$value;
            },
            'boolean' => function ($value) {
                return (bool)$value;
            },
            'int' => function ($value, $base = null) {
                return $base ? intval($value, (int)$base) : (int)$value;
            },
            'integer' => function ($value, $base = null) {
                return $base ? intval($value, (int)$base) : (int)$value;
            },
            'datetime' => function ($value) {
                return $this->castToPHPDate($value);
            },
            'date' => function ($value) {
                return $this->castToPHPDate($value)->setTime(0, 0, 0, 0);
            },
            'immutable_datetime' => function ($value) {
                return $this->castToPHPImmutableDate($value);
            },
            'immutable_date' => function ($value) {
                return $this->castToPHPImmutableDate($value)->setTime(0, 0, 0, 0);
            },
            'timestamp' => function ($value) {
                return $this->castToPHPDate($value)->getTimestamp();
            },
            'decimal' => function ($value, $decimals = 0) {
                return number_format($value, (int)$decimals, '.', '');
            },
            'float' => function ($value) {
                return $this->fromFloat($value);
            },
            'double' => function ($value) {
                return $this->fromFloat($value);
            },
            'real' => function ($value) {
                return $this->fromFloat($value);
            },
            'collection' => function ($value) {
                return function_exists('collect') ?
                    call_user_func('collect', $value) : (function_exists('\Drewlabs\Support\Proxy\Collection') ? call_user_func('\Drewlabs\Support\Proxy\Collection', $value) : $value);
            },
        ];
    }

    private function getCastTypeName($name)
    {
        if ($this->isParameterizedDateTimeCast($name)) {
            return 'datetime';
        }
        if ($this->isParameterizedImmutableDateTimeCast($name)) {
            return 'immutable_date';
        }

        if ($this->isDecimalCast($name)) {
            return 'decimal';
        }
        if ($this->isIntegerCast($name)) {
            return 'int';
        }
        return $name ? trim(strtolower($name)) : null;
    }
}
