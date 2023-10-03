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

use DateTimeInterface;
use Exception;
use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\HasAccessor;

/**
 * @property DateTimeInterface|string|null $datetime
 */
final class TypeControl implements HasAccessor
{
    /**
     * @uses get_datetime
     * @uses set_datetime
     */
    use AccessorTrait;

    /**
     * @var DateTimeInterface|string|null
     */
    private $datetime;

    /**
     * @throws Exception
     *
     * @return DateTimeInterface|null
     */
    private function get_datetime()
    {
        $datetime = $this->datetime;

        if (!$datetime) {
            return  null;
        }

        if (!$datetime instanceof DateTimeInterface) {
            $this->datetime = $datetime = new \DateTime($datetime);
        }

        return $datetime;
    }

    /**
     * @param mixed $datetime
     *
     * @return void
     */
    private function set_datetime($datetime)
    {
        $this->datetime = $datetime;
    }
}
