<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor\AccessorReflectionTest;

use ICanBoogie\Accessor\AccessorTrait;

class FacadeProperty
{
    use AccessorTrait;

    private $private;

    private $facade;

    protected function set_facade()
    {
    }

    protected function get_facade()
    {
    }
}
