<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Accessor;

use function ucfirst;

/**
 * Formats accessor method using CamelCase.
 */
trait FormatAsCamel
{
    public static function accessor_format(
        string $property,
        string $type,
        string $lazy = HasAccessor::ACCESSOR_IS_NOT_LAZY
    ): string {
        $format = $type . ucfirst($property);

        return $lazy ? $lazy . ucfirst($format) : $format;
    }
}
