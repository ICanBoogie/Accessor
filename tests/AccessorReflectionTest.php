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

use ICanBoogie\Accessor\AccessorReflectionTest\FacadeProperty;
use ICanBoogie\Accessor\AccessorReflectionTest\PrivateProperty;
use PHPUnit\Framework\TestCase;

class AccessorReflectionTest extends TestCase
{
	public function test_resolve_private_properties(): void
	{
		$a = new PrivateProperty;

		$properties = AccessorReflection::resolve_private_properties($a);

		$this->assertCount(1, $properties);
		$this->assertEquals('private', $properties[0]->name);

		$class_properties = AccessorReflection::resolve_private_properties(AccessorReflectionTest\PrivateProperty::class);

		$this->assertEquals($properties, $class_properties);
	}

	public function test_resolve_facade_properties(): void
	{
		$a = new FacadeProperty;

		$properties = AccessorReflection::resolve_facade_properties($a);

		$this->assertCount(1, $properties);
		$this->assertArrayHasKey('facade', $properties);
		$this->assertInstanceOf(\ReflectionProperty::class, $properties['facade']);

		$class_properties = AccessorReflection::resolve_facade_properties(AccessorReflectionTest\FacadeProperty::class);

		$this->assertEquals($properties, $class_properties);
	}
}
