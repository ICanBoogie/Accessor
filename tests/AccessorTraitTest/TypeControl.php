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
 * @property \DateTime $datetime
 */
class TypeControl implements HasAccessor
{
    use AccessorTrait;

    private $datetime;

    protected function set_datetime($datetime)
    {
        $this->datetime;
    }

    protected function get_datetime()
    {
        $datetime = $this->datetime;

        if (!($datetime instanceof \DateTime)) {
            $this->datetime = $datetime = new \DateTime($datetime);
        }

        return $datetime;
    }
}
