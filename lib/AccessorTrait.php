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
use function array_keys;
use function get_object_vars;
use function method_exists;
use function property_exists;

/**
 * Implements ICanBoogie's accessor pattern.
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
	 * The method tries to get the property using the getter and lazy getter methods.
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
	 * @throws PropertyNotReadable when the property is not accessible or is write-only
	 * (the property is not defined and only a setter is available).
	 */
	private function assert_property_is_readable($property)
	{
		try
		{
			$reflexion_class = new \ReflectionClass($this);
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

		$this->assert_no_accessor($property, HasAccessor::ACCESSOR_TYPE_SETTER, PropertyNotReadable::class);

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
	 * @throws PropertyNotWritable when the property doesn't exists, has no lazy getter and is
	 * not public; or when only a getter is implemented.
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

		$this->assert_no_accessor($property, HasAccessor::ACCESSOR_TYPE_GETTER, PropertyNotWritable::class);
	}

	/**
	 * Asserts that an accessor is not implemented.
	 *
	 * @param string $property
	 * @param string $type One of {@link HasAccessor::ACCESSOR_TYPE_GETTER}
	 * and {@link HasAccessor::ACCESSOR_TYPE_SETTER}.
	 * @param string $exception_class
	 */
	private function assert_no_accessor($property, $type, $exception_class)
	{
		if ($this->has_method(static::accessor_format($property, $type)))
		{
			throw new $exception_class([ $property, $this ]);
		}
	}
}
