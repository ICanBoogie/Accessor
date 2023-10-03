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
 * @property-read string $property
 */
class ReadOnlyVirtualProperty implements HasAccessor
{
    use AccessorTrait;

    public $another_property;

    protected function get_property()
    {
        return $this->another_property;
    }
}
