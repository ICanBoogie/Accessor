<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor;

use ICanBoogie\PropertyNotDefined;
use ICanBoogie\PropertyNotReadable;
use ICanBoogie\PropertyNotWritable;
use ReflectionException;

/**
 * Interface for classes implementing the accessor pattern.
 */
interface HasAccessor
{
    public const ACCESSOR_TYPE_GETTER = "get";
    public const ACCESSOR_TYPE_SETTER = "set";
    public const ACCESSOR_IS_LAZY = 'lazy';
    public const ACCESSOR_IS_NOT_LAZY = '';

    /**
     * Formats an accessor method name.
     *
     * @param string $type One of {@link ACCESSOR_TYPE_GETTER} and {@link ACCESSOR_TYPE_SETTER}.
     * @param string $lazy One of {@link ACCESSOR_IS_NOT_LAZY} and {@link ACCESSOR_IS_LAZY}.
     * Defaults to {@link ACCESSOR_IS_NOT_LAZY}.
     */
    public static function accessor_format(
        string $property,
        string $type,
        string $lazy = self::ACCESSOR_IS_NOT_LAZY
    ): string;

    /**
     * Returns the value of a property.
     *
     * @param string $property
     *
     * @return mixed
     *
     * @throws PropertyNotDefined when the property is not defined.
     * @throws PropertyNotReadable when the property is not accessible or is write-only
     * (the property is not defined and only a setter is available).
     * @throws ReflectionException
     */
    public function __get($property);

    /**
     * Sets the value of a property.
     *
     * @param string $property
     * @param mixed $value
     *
     * @return void
     *
     * @throws PropertyNotWritable when the property doesn't exists, has no lazy
     * getter and is not public; or when only a getter is implemented.
     */
    public function __set($property, $value);

    /**
     * Whether an object has a property.
     *
     * The property can be defined by the class or handled by a getter or setter, or both.
     */
    public function has_property(string $property): bool;

    /**
     * Whether an object has a method.
     */
    public function has_method(string $method): bool;
}
