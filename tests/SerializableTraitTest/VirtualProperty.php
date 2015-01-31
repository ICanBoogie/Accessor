<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor\SerializableTraitTest;

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\HasAccessor;
use ICanBoogie\Accessor\SerializableTrait;

/**
 * @package ICanBoogie\Accessor\AccessorTraitTest
 *
 * @property int $minutes
 */
class VirtualProperty implements HasAccessor
{
	use AccessorTrait;
	use SerializableTrait;

	public $seconds = 0;

	protected function set_minutes($minute)
	{
		$this->seconds = $minute * 60;
	}

	protected function get_minutes()
	{
		return $this->seconds / 60;
	}
}
