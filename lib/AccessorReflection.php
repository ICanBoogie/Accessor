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

use function array_merge;
use function get_class;
use function is_object;
use function method_exists;

/**
 * Provides methods to reflect on accessor.
 */
final class AccessorReflection
{
	static private $private_properties_cache = [];
	static private $facade_properties_cache = [];

	/**
	 * @param string|object $reference
	 *
	 * @return string
	 */
	static private function resolve_reference($reference)
	{
		if (is_object($reference))
		{
			return get_class($reference);
		}

		return $reference;
	}

	/**
	 * Returns the private properties defined by the reference, this includes the private
	 * properties defined by the whole class inheritance.
	 *
	 * @param string|object $reference Class name or instance.
	 *
	 * @return \ReflectionProperty[]
	 *
	 * @throws \ReflectionException
	 */
	static public function resolve_private_properties($reference)
	{
		$reference = self::resolve_reference($reference);
		$cached = &self::$private_properties_cache[$reference];

		if ($cached)
		{
			return $cached;
		}

		$private_properties = [];
		$class_reflection = new \ReflectionClass($reference);

		while ($class_reflection)
		{
			$private_properties[] = $class_reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
			$class_reflection = $class_reflection->getParentClass();
		}

		return $cached = $private_properties
			? array_merge(...$private_properties)
			: [];
	}

	/**
	 * Returns the façade properties implemented by the specified reference.
	 *
	 * A façade property is a combination of a private property with the corresponding volatile
	 * getter and setter.
	 *
	 * @param string|HasAccessor $reference Class name or instance implementing {@link HasAccessor}.
	 *
	 * @return \ReflectionProperty[]
	 *
	 * @throws \ReflectionException
	 */
	static public function resolve_facade_properties($reference)
	{
		$reference = self::resolve_reference($reference);
		$facade_properties = &self::$facade_properties_cache[$reference];

		if ($facade_properties)
		{
			return $facade_properties;
		}

		$facade_properties = [];

		foreach (self::resolve_private_properties($reference) as $property)
		{
			$name = $property->name;

			if (!method_exists($reference, $reference::accessor_format($name, HasAccessor::ACCESSOR_TYPE_GETTER))
			|| !method_exists($reference, $reference::accessor_format($name, HasAccessor::ACCESSOR_TYPE_SETTER)))
			{
				continue;
			}

			$facade_properties[$name] = $property;
		}

		return $facade_properties;
	}
}
