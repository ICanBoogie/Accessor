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
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

use function array_keys;
use function get_class;
use function get_object_vars;
use function implode;
use function method_exists;
use function property_exists;
use function sprintf;

/**
 * Implements ICanBoogie's accessor pattern.
 */
trait AccessorTrait
{
	use FormatAsSnake;

	public function __get($property)
	{
		return $this->accessor_get($property);
	}

	public function __set($property, $value)
	{
		$this->accessor_set($property, $value);
	}

	public function has_property(string $property): bool
	{
		return property_exists($this, $property)
			|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER))
			|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY))
			|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER))
			|| $this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_SETTER, HasAccessor::ACCESSOR_IS_LAZY));
	}

	public function has_method(string $method): bool
	{
		return method_exists($this, $method);
	}

	/**
	 * Returns the value of an inaccessible property.
	 *
	 * The method tries to get the property using the getter and lazy getter methods.
	 *
	 * @return mixed
	 */
	private function accessor_get(string $property)
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
	 * @param mixed $value
	 *
	 * @throws ReflectionException
	 */
	private function accessor_set(string $property, $value): void
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
	 * @throws PropertyNotDefined when the property is not defined.
	 * @throws PropertyNotReadable when the property is not accessible or is write-only
	 * (the property is not defined and only a setter is available).
	 */
	private function assert_property_is_readable(string $property): void
	{
		try
		{
			$reflexion_class = new ReflectionClass($this);
			$reflexion_property = $reflexion_class->getProperty($property);

			if (!$reflexion_property->isPublic())
			{
				throw new PropertyNotReadable([ $property, $this ]);
			}
		}
		catch (ReflectionException $e)
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
	 * @throws PropertyNotWritable|ReflectionException when the property doesn't exist, has no lazy getter and is
	 * not public; or when only a getter is implemented.
	 */
	private function assert_property_is_writable(string $property): void
	{
		if (property_exists($this, $property) && !$this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY)))
		{
			$reflection = new ReflectionObject($this);
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
	 * @param string $type One of {@link HasAccessor::ACCESSOR_TYPE_GETTER}
	 * and {@link HasAccessor::ACCESSOR_TYPE_SETTER}.
	 */
	private function assert_no_accessor(string $property, string $type, string $exception_class): void
	{
		if ($this->has_method(static::accessor_format($property, $type)))
		{
			throw new $exception_class([ $property, $this ]);
		}
	}
}
