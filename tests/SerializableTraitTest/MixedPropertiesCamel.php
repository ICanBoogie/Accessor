<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor\SerializableTraitTest;

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Accessor\AccessorCamelTrait;
use ICanBoogie\Accessor\SerializableTrait;

class MixedPropertiesCamel
{
    use AccessorCamelTrait;
    use SerializableTrait;

    public $public = 'public';
    public $publicWithLazyGetter = 'publicWithLazyGetter';

    protected function lazyGetPublicWithLazyGetter()
    {
        return 'VALUE: publicWithLazyGetter';
    }

    protected $protected = 'protected';
    protected $protectedWithGetter = 'protectedWithGetter';
    protected $protectedWithSetter = 'protectedWithSetter';
    protected $protectedWithGetterAndSetter = 'protectedWithGetterAndSetter';
    protected $protectedWithLazyGetter = 'protectedWithLazyGetter';

    protected function getProtectedWithGetter()
    {
        return 'VALUE: protectedWithGetter';
    }

    protected function setProtectedWithSetter()
    {
    }

    protected function getProtectedWithGetterAndSetter()
    {
        return 'VALUE: protectedWithGetter';
    }

    protected function setProtectedWithGetterAndSetter()
    {
    }

    protected function lazyGetProtectedWithLazyGetter()
    {
        return 'VALUE: protectedWithLazyGetter';
    }
}
