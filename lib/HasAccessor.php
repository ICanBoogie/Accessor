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
