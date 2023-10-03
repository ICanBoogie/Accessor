<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Accessor\SerializableTraitTest;

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\HasAccessor;
use ICanBoogie\Accessor\SerializableTrait;

/**
 * @property \DateTime $datetime
 */
class DateTimeProperty implements HasAccessor
{
    use AccessorTrait;
    use SerializableTrait;

    private $datetime;

    protected function set_datetime($datetime)
    {
        $this->datetime = $datetime;
    }

    protected function get_datetime()
    {
        $datetime = $this->datetime;

        if ($datetime instanceof \DateTime) {
            return $datetime;
        }

        return $this->datetime = $datetime === null ? new \DateTime('0000-00-00') : new \DateTime($datetime);
    }
}
