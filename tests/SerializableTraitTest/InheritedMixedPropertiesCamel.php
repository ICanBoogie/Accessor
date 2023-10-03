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

class InheritedMixedPropertiesCamel extends MixedPropertiesCamel
{
    private $private = 'private';
    private $privateWithGetter = 'privateWithGetter';
    private $privateWithSetter = 'privateWithSetter';
    private $privateWithGetterAndSetter = 'privateWithGetterAndSetter';
    private $privateWithLazyGetter = 'privateWithLazyGetter';

    protected function getPrivateWithGetter()
    {
        return 'VALUE: privateWithGetter';
    }

    protected function setPrivateWithSetter()
    {
    }

    protected function getPrivateWithGetterAndSetter()
    {
        return 'VALUE: privateWithGetter';
    }

    protected function setPrivateWithGetterAndSetter()
    {
    }

    protected function lazyGetPrivateWithLazyGetter()
    {
        return 'VALUE: privateWithLazyGetter';
    }
}
