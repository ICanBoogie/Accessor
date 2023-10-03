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
 * @property mixed $property
 */
#[\AllowDynamicProperties]
class LazyProperty implements HasAccessor
{
    use AccessorTrait;

    protected function lazy_get_property()
    {
        return "success";
    }
}
