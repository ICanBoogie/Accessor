<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Accessor\AccessorReflectionTest;

use ICanBoogie\Accessor\AccessorTrait;

class PrivateProperty
{
    use AccessorTrait;

    public $public;
    protected $protected;
    private $private;
}
