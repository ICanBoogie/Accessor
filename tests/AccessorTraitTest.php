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

use ICanBoogie\Accessor\AccessorTraitTest\DefaultProperty;
use ICanBoogie\Accessor\AccessorTraitTest\HasPropertyFixture;
use ICanBoogie\Accessor\AccessorTraitTest\HasPropertyFixtureCamel;
use ICanBoogie\Accessor\AccessorTraitTest\LazyProperty;
use ICanBoogie\Accessor\AccessorTraitTest\LazySetProperty;
use ICanBoogie\Accessor\AccessorTraitTest\PropertyList;
use ICanBoogie\Accessor\AccessorTraitTest\ReadOnlyPrivateProperty;
use ICanBoogie\Accessor\AccessorTraitTest\ReadOnlyProtectedProperty;
use ICanBoogie\Accessor\AccessorTraitTest\ReadOnlyVirtualProperty;
use ICanBoogie\Accessor\AccessorTraitTest\TypeControl;
use ICanBoogie\Accessor\AccessorTraitTest\UndefinedProperty;
use ICanBoogie\Accessor\AccessorTraitTest\UnsetPublicProperty;
use ICanBoogie\Accessor\AccessorTraitTest\VirtualProperty;
use ICanBoogie\Accessor\AccessorTraitTest\WriteOnlyPrivateProperty;
use ICanBoogie\Accessor\AccessorTraitTest\WriteOnlyProtectedProperty;
use ICanBoogie\Accessor\AccessorTraitTest\WriteOnlyVirtualProperty;
use ICanBoogie\PropertyNotDefined;

class AccessorTraitTest extends \PHPUnit_Framework_TestCase
{
	/*
	 * Undefined property
	 */

	public function test_should_set_undefined_property()
	{
		$a = new UndefinedProperty;
		$expected = uniqid();
		$a->undefined = $expected;
		$this->assertEquals($expected, $a->undefined);
	}

	public function test_should_set_unset_public_property()
	{
		$a = new UnsetPublicProperty;
		unset($a->property);
		$expected = uniqid();
		$a->property = $expected;
		$this->assertEquals($expected, $a->property);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotDefined
	 */
	public function test_getting_undefined_property_must_throw_an_exception()
	{
		$a = new UndefinedProperty;
		$a->undefined;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotDefined
	 */
	public function test_getting_unset_property_must_throw_an_exception()
	{
		$a = new UnsetPublicProperty;
		unset($a->property);
		$a->property;
	}

	/*
	 * Read-only properties
	 */

	public function test_should_get_readonly_private_property()
	{
		$a = new ReadOnlyPrivateProperty;
		$this->assertEquals("success", $a->property);
	}

	public function test_should_get_readonly_protected_property()
	{
		$a = new ReadOnlyProtectedProperty;
		$this->assertEquals("success", $a->property);
	}

	public function test_should_get_readonly_virtual_property()
	{
		$a = new ReadOnlyVirtualProperty;
		$expected = uniqid();
		$a->another_property = $expected;
		$this->assertEquals($expected, $a->property);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_setting_readonly_private_property_must_throw_an_exception()
	{
		$a = new ReadOnlyPrivateProperty;
		$a->property = null;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_setting_readonly_protected_property_must_throw_an_exception()
	{
		$a = new ReadOnlyProtectedProperty;
		$a->property = null;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_setting_readonly_virtual_property_must_throw_an_exception()
	{
		$a = new ReadOnlyVirtualProperty;
		$a->property = null;
	}

	/*
	 * Write-only properties
	 */

	public function test_should_set_writeonly_private_property()
	{
		$a = new WriteOnlyPrivateProperty;
		$expected = uniqid();
		$a->property = $expected;
		$this->assertEquals($expected, $a->check());
	}

	public function test_should_set_writeonly_protected_property()
	{
		$a = new WriteOnlyProtectedProperty;
		$expected = uniqid();
		$a->property = $expected;
		$this->assertEquals($expected, $a->check());
	}

	public function test_should_set_writeonly_virtual_property()
	{
		$a = new WriteOnlyVirtualProperty;
		$expected = uniqid();
		$a->property = $expected;
		$this->assertEquals($expected, $a->another_property);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotReadable
	 */
	public function test_getting_writeonly_private_property_should_throw_an_exception()
	{
		$a = new WriteOnlyPrivateProperty;
		$a->property;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotReadable
	 */
	public function test_getting_writeonly_protected_property_should_throw_an_exception()
	{
		$a = new WriteOnlyProtectedProperty;
		$a->property;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotReadable
	 */
	public function test_getting_writeonly_virtual_property_should_throw_an_exception()
	{
		$a = new WriteOnlyVirtualProperty;
		$a->property;
	}

	/*
	 * Virtual properties
	 */

	public function test_virtual_property()
	{
		$a = new VirtualProperty;
		$this->assertEquals(0, $a->seconds);
		$a->minutes = 1;
		$this->assertEquals(60, $a->seconds);
		$a->seconds = 120;
		$this->assertEquals(2, $a->minutes);
		$this->assertArrayHasKey('seconds', (array) $a);
		$this->assertArrayNotHasKey('minutes', (array) $a);
	}

	public function test_type_control()
	{
		$a = new TypeControl;
		$this->assertInstanceOf('DateTime', $a->datetime);
		$a->datetime = 'now';
		$this->assertInstanceOf('DateTime', $a->datetime);
	}

	/*
	 * Default provider
	 */

	public function test_should_provide_default_value_but_not_create_property()
	{
		$a = new DefaultProperty;
		$this->assertEmpty($a->value);
		$this->assertArrayHasKey('value', (array) $a);
		unset($a->value);
		$this->assertEquals('default-value', $a->value);
		$this->assertArrayNotHasKey('value', (array) $a);
		$expected = uniqid();
		$a->value = $expected;
		$this->assertEquals($expected, $a->value);
		$this->assertArrayHasKey('value', (array) $a);
		$this->assertEquals([ 'value' => $expected ], (array) $a);
	}

	/*
	 * Lazy getter
	 */

	public function test_lazy_getter_should_create_property()
	{
		$a = new LazyProperty;
		$this->assertArrayNotHasKey('property', (array) $a);
		$this->assertEquals("success", $a->property);
		$this->assertArrayHasKey('property', (array) $a);
		unset($a->property);
		$this->assertArrayNotHasKey('property', (array) $a);
		$this->assertEquals("success", $a->property);
		$this->assertArrayHasKey('property', (array) $a);
		unset($a->property);
		$this->assertArrayNotHasKey('property', (array) $a);
		$expected = uniqid();
		$a->property = $expected;
		$this->assertEquals($expected, $a->property);
		$this->assertEquals([ 'property' => $expected ], (array) $a);
	}

	public function test_should_have_properties()
	{
		$a = new HasPropertyFixture;
		$this->assertTrue($a->has_property('public'));
		$this->assertTrue($a->has_property('protected'));
		$this->assertTrue($a->has_property('private'));
		$this->assertTrue($a->has_property('unset_public'));
		$this->assertTrue($a->has_property('unset_protected'));
		$this->assertTrue($a->has_property('unset_private'));
		$this->assertTrue($a->has_property('readonly'));
		$this->assertTrue($a->has_property('lazy_readonly'));
		$this->assertTrue($a->has_property('writeonly'));
		$this->assertTrue($a->has_property('lazy_writeonly'));

		$a->dynamic = true;
		$this->assertTrue($a->has_property('dynamic'));
	}

	public function test_should_have_properties_as_camel()
	{
		$a = new HasPropertyFixtureCamel;
		$this->assertTrue($a->has_property('public'));
		$this->assertTrue($a->has_property('protected'));
		$this->assertTrue($a->has_property('private'));
		$this->assertTrue($a->has_property('unsetPublic'));
		$this->assertTrue($a->has_property('unsetProtected'));
		$this->assertTrue($a->has_property('unsetPrivate'));
		$this->assertTrue($a->has_property('readonly'));
		$this->assertTrue($a->has_property('lazyReadonly'));
		$this->assertTrue($a->has_property('writeonly'));
		$this->assertTrue($a->has_property('lazyWriteonly'));

		$a->dynamic = true;
		$this->assertTrue($a->has_property('dynamic'));
	}

	public function test_should_not_have_property()
	{
		$a = new HasPropertyFixture;
		$this->assertFalse($a->has_property('undefined'));
	}

	public function test_lazy_set_should_be_invoked()
	{
		$a = new LazySetProperty;
		$a->property = 'testing';
		$this->assertEquals("TESTING", $a->property);
	}

	public function test_undefined_get_should_give_property_list()
	{
		$a = new PropertyList;

		try
		{
			$a->undefined;

			$this->fail("Expected PropertyNotDefined");
		}
		catch (PropertyNotDefined $e)
		{
			$this->assertContains("available properties: public, protected, private", $e->getMessage());
		}
	}
}
