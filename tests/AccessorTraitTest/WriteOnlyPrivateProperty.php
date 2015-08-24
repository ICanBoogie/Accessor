<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor\AccessorTraitTest;

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\HasAccessor;

/**
 * @property-write string $property
 */
class WriteOnlyPrivateProperty implements HasAccessor
{
	use AccessorTrait;

	private $property;

	protected function set_property($value)
	{
		$this->property = $value;
	}

	public function check()
	{
		return $this->property;
	}
}
