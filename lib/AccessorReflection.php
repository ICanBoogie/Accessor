<?php

namespace ICanBoogie\Accessor;

class AccessorReflection
{
	static private $private_properties_cache = [];
	static private $facade_properties_cache = [];

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
	 */
	static public function resolve_private_properties($reference)
	{
		$reference = self::resolve_reference($reference);

		if (isset(self::$private_properties_cache[$reference]))
		{
			return self::$private_properties_cache[$reference];
		}

		$private_properties = [];
		$class_reflection = new \ReflectionClass($reference);

		while ($class_reflection)
		{
			$private_properties[] = $class_reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
			$class_reflection = $class_reflection->getParentClass();
		}

		return self::$private_properties_cache[$reference] = $private_properties ? call_user_func_array('array_merge', $private_properties) : [];
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
	 */
	static public function resolve_facade_properties($reference)
	{
		$reference = self::resolve_reference($reference);

		if (isset(self::$facade_properties_cache[$reference]))
		{
			return self::$facade_properties_cache[$reference];
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

		return self::$facade_properties_cache[$reference] = $facade_properties;
	}
}
