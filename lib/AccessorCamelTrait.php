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
 * An accessor trait using camel casing.
 *
 * @package ICanBoogie\Accessor
 */
trait AccessorCamelTrait
{
	use AccessorTrait, FormatAsCamel
	{
		FormatAsCamel::accessor_format insteadof AccessorTrait;
	}
}
