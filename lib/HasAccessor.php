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

/**
 * Interface for classes implementing the accessor pattern.
 *
 * @package ICanBoogie\Accessor
 */
interface HasAccessor
{
	const ACCESSOR_TYPE_GETTER = "get";
	const ACCESSOR_TYPE_SETTER = "set";
	const ACCESSOR_IS_LAZY = 'lazy';
	const ACCESSOR_IS_NOT_LAZY = '';

	/**
	 * Formats an accessor method name.
	 *
	 * @param string $property A property.
	 * @param string $type One of {@link ACCESSOR_TYPE_GETTER} and {@link ACCESSOR_TYPE_SETTER}.
	 * @param string $lazy One of {@link ACCESSOR_IS_NOT_LAZY} and {@link ACCESSOR_IS_LAZY}.
	 * Defaults to {@link ACCESSOR_IS_NOT_LAZY}.
	 *
	 * @return mixed
	 */
	static public function accessor_format($property, $type, $lazy = self::ACCESSOR_IS_NOT_LAZY);

	/**
	 * Returns the value of a property.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property);

	/**
	 * Sets the value of a property.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set($property, $value);

	/**
	 * Whether an object has a property.
	 *
	 * @param string $property
	 *
	 * @return bool `true` if the object has a property, `false` otherwise.
	 */
	public function has_property($property);

	/**
	 * Whether an object has a method.
	 *
	 * @param string $method
	 *
	 * @return bool `true` if the object has a method, `false` otherwise.
	 */
	public function has_method($method);
}
