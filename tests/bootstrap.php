<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4("ICanBoogie\\Accessor\\AccessorTraitTest\\", __DIR__ . '/AccessorTraitTest');
$loader->addPsr4("ICanBoogie\\Accessor\\AccessorReflectionTest\\", __DIR__ . '/AccessorReflectionTest');
$loader->addPsr4("ICanBoogie\\Accessor\\SerializableTraitTest\\", __DIR__ . '/SerializableTraitTest');
