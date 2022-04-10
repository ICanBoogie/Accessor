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

use ICanBoogie\Accessor\AccessorCamelTrait;

class HasPropertyFixtureCamel
{
    use AccessorCamelTrait;

    public $public;
    protected $protected;
    private $private;

    public $unsetPublic;
    protected $unsetProtected;
    private $unsetPrivate;

    public function __construct()
    {
        unset($this->unsetPublic);
        unset($this->unsetProtected);
        unset($this->unsetPrivate);
    }

    protected function getReadonly()
    {
    }

    protected function lazyGetLazyReadonly()
    {
    }

    protected function setWriteonly()
    {
    }

    protected function lazySetLazyWriteonly()
    {
    }
}
