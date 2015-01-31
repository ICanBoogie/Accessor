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

class HasPropertyFixture
{
	use AccessorTrait;

	public $public;
	protected $protected;
	private $private;

	public $unset_public;
	protected $unset_protected;
	private $unset_private;

	public function __construct()
	{
		unset($this->unset_public);
		unset($this->unset_protected);
		unset($this->unset_private);
	}

	protected function get_readonly()
	{

	}

	protected function lazy_get_lazy_readonly()
	{

	}

	protected function set_writeonly()
	{

	}

	protected function lazy_set_lazy_writeonly()
	{

	}
}
