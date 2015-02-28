<?php

namespace ICanBoogie\Accessor;

/**
 * Improves serialization of objects, exporting façade properties and removing properties for
 * which lazy getters are defined.
 *
 * @package ICanBoogie\Accessor
 */
trait SerializableTrait
{
	abstract /*static*/ public function accessor_format($property, $type, $lazy = HasAccessor::ACCESSOR_IS_NOT_LAZY);

	/**
	 * @inheritdoc
	 */
	public function __sleep()
	{
		return $this->accessor_sleep();
	}

	/**
	 * @inheritdoc
	 */
	public function __wakeup()
	{
		$this->accessor_wakeup();
	}

	/**
	 * Whether an object has a method.
	 *
	 * @param string $method
	 *
	 * @return bool `true` if the object has a method, `false` otherwise.
	 */
	abstract protected function has_method($method);

	/**
	 * The method returns an array of key/key pairs.
	 *
	 * Properties for which a lazy getter is defined are discarded. For instance, if the property
	 * `next` is defined and the class of the instance defines the getter `lazy_get_next()`, the
	 * property is discarded.
	 *
	 * Note that façade properties are also included.
	 *
	 * @return array
	 */
	private function accessor_sleep()
	{
		$properties = array_keys(get_object_vars($this));

		if ($properties)
		{
			$properties = array_combine($properties, $properties);

			foreach ($properties as $property)
			{
				if ($this->has_method(static::accessor_format($property, HasAccessor::ACCESSOR_TYPE_GETTER, HasAccessor::ACCESSOR_IS_LAZY)))
				{
					unset($properties[$property]);
				}
			}
		}

		foreach (AccessorReflection::resolve_facade_properties($this) as $name => $property)
		{
			$properties[$name] = "\x00" . $property->class . "\x00" . $name;
		}

		return $properties;
	}

	/**
	 * Unsets null properties for which a lazy getter is defined so that it is called when
	 * the property is accessed.
	 */
	public function accessor_wakeup()
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
