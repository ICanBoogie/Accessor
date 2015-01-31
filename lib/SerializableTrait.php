<?php

namespace ICanBoogie\Accessor;

trait SerializableTrait
{
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
	public function __sleep()
	{
		$keys = array_keys(get_object_vars($this));

		if ($keys)
		{
			$keys = array_combine($keys, $keys);

			foreach ($keys as $key)
			{
				if ($this->has_method('lazy_get_' . $key))
				{
					unset($keys[$key]);
				}
			}
		}

		foreach (AccessorReflection::resolve_facade_properties($this) as $name => $property)
		{
			$keys[$name] = "\x00" . $property->class . "\x00" . $name;
		}

		return $keys;
	}

	/**
	 * Unsets null properties for which a lazy getter is defined so that it is called when
	 * the property is accessed.
	 */
	public function __wakeup()
	{
		$vars = get_object_vars($this);

		foreach ($vars as $key => $value)
		{
			if ($this->has_method('lazy_get_' . $key))
			{
				unset($this->$key);
			}
		}
	}
}
