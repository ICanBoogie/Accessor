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

class LazySetProperty
{
    use AccessorTrait;

    protected $property;

    protected function lazy_set_property($value)
    {
        return strtoupper($value);
    }

    protected function get_property()
    {
        return $this->property;
    }

    public function __constructor()
    {
        unset($this->property);
    }
}
