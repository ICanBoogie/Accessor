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

/**
 * Implements an accessor pattern.
 *
 * @package ICanBoogie\Object
 */
trait AccessorTrait
{
	use FormatAsSnake;

	/**
	 * @inheritdoc
	 */
	public function __get($property)
	{
		return $this->accessor_get($property);
	}

	/**
	 * @inheritdoc
	 */
	public function __set($property, $value)
	{
		$this->accessor_set($property, $value);
	}

	/**
	 * Whether an object has a property.
	 *
	 * The property can be defined by the class or handled by a getter or setter, or both.
	 *
	 * @param string $property
	 *
	 * @return bool `true` if the object has a property, `false` otherwise.
	 */
	public function has_property($property)
	{
		return property_exists($this, $property)
		|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER))
		|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY))
		|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER))
		|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER, HasAccessor::ACCESSOR_IS_LAZY));
	}

	/**
	 * Whether an object has a method.
	 *
	 * @param string $method
	 *
	 * @return bool `true` if the object has a method, `false` otherwise.
	 */
	public function has_method($method)
	{
		return method_exists($this, $method);
	}

	/**
	 * Returns the value of an inaccessible property.
	 *
	 * Multiple callbacks are tried in order to retrieve the value of the property:
	 *
	 * 1. `get_<property>`: Get and return the value of the property.
	 * 2. `lazy_get_<property>`: Get, set and return the value of the property. Because new
	 * properties are created as public the callback is only called once which is ideal for lazy
	 * loading.
	 * 3. The prototype is queried for callbacks for the `get_<property>` and
	 * `lazy_get_<property>` methods.
	 * 4. Finally, the `ICanBoogie\Object::property` event is fired to try and retrieve the value
	 * of the property.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	private function accessor_get($property)
	{
		$method = static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER);

		if ($this->has_method($method))
		{
			return $this->$method();
		}

		$method = static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY);

		if ($this->has_method($method))
		{
			return $this->$property = $this->$method();
		}

		$this->assert_property_is_readable($property);
	} //@codeCoverageIgnore

	/**
	 * Sets the value of an inaccessible property.
	 *
	 * The method is called because the property does not exists, its visibility is
	 * _protected_ or _private_, or because although it is visible is was unset and is no
	 * longer accessible.
	 *
	 * A `set_<property>` method can be used the handle virtual properties, for instance a
	 * `minute` property that would alter a `second` property.
	 *
	 * A `lazy_set_<property>` method can be used to set properties that are protected or
	 * private, which can be used to make properties write-only for example.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	private function accessor_set($property, $value)
	{
		$method = static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER);

		if ($this->has_method($method))
		{
			$this->$method($value);

			return;
		}

		$method = static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER, HasAccessor::ACCESSOR_IS_LAZY);

		if ($this->has_method($method))
		{
			$this->$property = $this->$method($value);

			return;
		}

		$this->assert_property_is_writable($property);

		$this->$property = $value;
	}

	/**
	 * Asserts that a property is readable.
	 *
	 * @param string $property
	 *
	 * @throws PropertyNotDefined when the property is not defined.
	 * @throws PropertyNotReadable when the property is not accessible or is write-only (the
	 * property is not defined and only a setter is available).
	 */
	private function assert_property_is_readable($property)
	{
		$reflexion_class = new \ReflectionClass($this);

		try
		{
			$reflexion_property = $reflexion_class->getProperty($property);

			if (!$reflexion_property->isPublic())
			{
				throw new PropertyNotReadable([ $property, $this ]);
			}
		}
		catch (\ReflectionException $e)
		{
			#
			# An exception may occur if the property is not defined, we don't care about that.
			#
		}

		if ($this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER)))
		{
			throw new PropertyNotReadable([ $property, $this ]);
		}

		$properties = array_keys(get_object_vars($this));

		if ($properties)
		{
			throw new PropertyNotDefined(sprintf('Unknown or inaccessible property "%s" for object of class "%s" (available properties: %s).', $property, get_class($this), implode(', ', $properties)));
		}

		throw new PropertyNotDefined([ $property, $this ]);
	}

	/**
	 * Asserts that a property is writable.
	 *
	 * @param string $property
	 *
	 * @throws PropertyNotWritable
	 */
	private function assert_property_is_writable($property)
	{
		if (property_exists($this, $property) && !$this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY)))
		{
			$reflection = new \ReflectionObject($this);
			$property_reflection = $reflection->getProperty($property);

			if (!$property_reflection->isPublic())
			{
				throw new PropertyNotWritable([ $property, $this ]);
			}

			return;
		}

		if ($this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER)))
		{
			throw new PropertyNotWritable([ $property, $this ]);
		}
	}
}
