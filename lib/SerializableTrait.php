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

use ReflectionException;

use function array_combine;
use function array_keys;
use function get_object_vars;

/**
 * Improves serialization of objects, exporting façade properties and removing properties for
 * which lazy getters are defined.
 *
 * @see HasAccessor
 */
trait SerializableTrait
{
	/**
	 * @throws ReflectionException
	 */
	public function __sleep()
	{
		return $this->accessor_sleep();
	}

	public function __wakeup()
	{
		$this->accessor_wakeup();
	}

	/**
	 * The method returns an array of key/key pairs.
	 *
	 * Properties for which a lazy getter is defined are discarded. For instance, if the property
	 * `next` is defined and the class of the instance defines the getter `lazy_get_next()`, the
	 * property is discarded.
	 *
	 * Note that façade properties are also included.
	 *
	 * @throws ReflectionException
	 */
	private function accessor_sleep(): array
	{
		$properties = array_keys(get_object_vars($this));

		if ($properties)
		{
			$properties = array_combine($properties, $properties);

			foreach ($properties as $property)
			{
				if ($this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY)))
				{
					unset($properties[ $property ]);
				}
			}
		}

		foreach (AccessorReflection::resolve_facade_properties($this) as $name => $property)
		{
			$properties[ $name ] = "\x00" . $property->class . "\x00" . $name;
		}

		return $properties;
	}

	/**
	 * Unsets null properties for which a lazy getter is defined so that it is called when
	 * the property is accessed.
	 */
	private function accessor_wakeup(): void
	{
		$properties = get_object_vars($this);

		foreach ($properties as $property => $value)
		{
			if ($this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY)))
			{
				unset($this->$property);
			}
		}
	}
}
