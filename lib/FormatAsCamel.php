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

trait FormatAsCamel
{
	/**
	 * @inheritdoc
	 */
	static public function accessor_format($property, $type, $lazy = HasAccessor::ACCESSOR_IS_NOT_LAZY)
	{
		$format = $type . ucfirst($property);

		return $lazy ? $lazy . ucfirst($format) : $format;
	}
}
