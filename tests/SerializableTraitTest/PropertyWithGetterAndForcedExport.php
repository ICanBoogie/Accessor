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

class B implements HasAccessor
{
	use AccessorTrait;
	use SerializableTrait;
}

/**
 * @property int $minutes
 */
class PropertyWithGetterAndForcedExport extends B
{
	public $property = "value";

	public function __sleep()
	{
		return parent::__sleep() + [

			'property' => 'property'

		];
	}

	protected function lazy_get_property()
	{
		return "lazy_value";
	}
}
