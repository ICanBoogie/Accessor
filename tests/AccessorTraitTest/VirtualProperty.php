<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Accessor\AccessorTraitTest;

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\HasAccessor;

/**
 * @property int $minutes
 */
class VirtualProperty implements HasAccessor
{
    use AccessorTrait;

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
