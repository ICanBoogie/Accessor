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
 * Formats accessor method using snake case.
 */
trait FormatAsSnake
{
	/**
	 * @inheritdoc
	 */
	static public function accessor_format($property, $type, $lazy = HasAccessor::ACCESSOR_IS_NOT_LAZY)
	{
		return ($lazy ? $lazy . '_' : '') . $type . '_' . $property;
	}
}
