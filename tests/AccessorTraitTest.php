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
use ICanBoogie\PropertyNotReadable;
use ICanBoogie\PropertyNotWritable;
use PHPUnit\Framework\TestCase;

class AccessorTraitTest extends TestCase
{
    /*
     * Undefined property
     */

    public function test_should_set_undefined_property(): void
    {
        $a = new UndefinedProperty();
        $expected = uniqid();
        $a->undefined = $expected;
        $this->assertEquals($expected, $a->undefined);
    }

    public function test_should_set_unset_public_property(): void
    {
        $a = new UnsetPublicProperty();
        unset($a->property);
        $expected = uniqid();
        $a->property = $expected;
        $this->assertEquals($expected, $a->property);
    }

    public function test_getting_undefined_property_must_throw_an_exception(): void
    {
        $a = new UndefinedProperty();
        $this->expectException(PropertyNotDefined::class);
        $a->undefined;
    }

    public function test_getting_unset_property_must_throw_an_exception(): void
    {
        $a = new UnsetPublicProperty();
        unset($a->property);
        $this->expectException(PropertyNotDefined::class);
        $a->property;
    }

    /*
     * Read-only properties
     */

    public function test_should_get_readonly_private_property(): void
    {
        $a = new ReadOnlyPrivateProperty();
        $this->assertEquals("success", $a->property);
    }

    public function test_should_get_readonly_protected_property(): void
    {
        $a = new ReadOnlyProtectedProperty();
        $this->assertEquals("success", $a->property);
    }

    public function test_should_get_readonly_virtual_property(): void
    {
        $a = new ReadOnlyVirtualProperty();
        $expected = uniqid();
        $a->another_property = $expected;
        $this->assertEquals($expected, $a->property);
    }

    public function test_setting_readonly_private_property_must_throw_an_exception(): void
    {
        $a = new ReadOnlyPrivateProperty();
        $this->expectException(PropertyNotWritable::class);
        $a->property = null;
    }

    public function test_setting_readonly_protected_property_must_throw_an_exception(): void
    {
        $a = new ReadOnlyProtectedProperty();
        $this->expectException(PropertyNotWritable::class);
        $a->property = null;
    }

    public function test_setting_readonly_virtual_property_must_throw_an_exception(): void
    {
        $a = new ReadOnlyVirtualProperty();
        $this->expectException(PropertyNotWritable::class);
        $a->property = null;
    }

    /*
     * Write-only properties
     */

    public function test_should_set_writeonly_private_property(): void
    {
        $a = new WriteOnlyPrivateProperty();
        $expected = uniqid();
        $a->property = $expected;
        $this->assertEquals($expected, $a->check());
    }

    public function test_should_set_writeonly_protected_property(): void
    {
        $a = new WriteOnlyProtectedProperty();
        $expected = uniqid();
        $a->property = $expected;
        $this->assertEquals($expected, $a->check());
    }

    public function test_should_set_writeonly_virtual_property(): void
    {
        $a = new WriteOnlyVirtualProperty();
        $expected = uniqid();
        $a->property = $expected;
        $this->assertEquals($expected, $a->another_property);
    }

    public function test_getting_writeonly_private_property_should_throw_an_exception(): void
    {
        $a = new WriteOnlyPrivateProperty();
        $this->expectException(PropertyNotReadable::class);
        $a->property;
    }

    public function test_getting_writeonly_protected_property_should_throw_an_exception(): void
    {
        $a = new WriteOnlyProtectedProperty();
        $this->expectException(PropertyNotReadable::class);
        $a->property;
    }

    public function test_getting_writeonly_virtual_property_should_throw_an_exception(): void
    {
        $a = new WriteOnlyVirtualProperty();
        $this->expectException(PropertyNotReadable::class);
        $a->property;
    }

    /*
     * Virtual properties
     */

    public function test_virtual_property(): void
    {
        $a = new VirtualProperty();
        $this->assertEquals(0, $a->seconds);
        $a->minutes = 1;
        $this->assertEquals(60, $a->seconds);
        $a->seconds = 120;
        $this->assertEquals(2, $a->minutes);
        $this->assertArrayHasKey('seconds', (array) $a);
        $this->assertArrayNotHasKey('minutes', (array) $a);
    }

    public function test_type_control(): void
    {
        $a = new TypeControl();
        $this->assertInstanceOf(\DateTime::class, $a->datetime);
        $a->datetime = 'now';
        $this->assertInstanceOf(\DateTime::class, $a->datetime);
    }

    /*
     * Default provider
     */

    public function test_should_provide_default_value_but_not_create_property(): void
    {
        $a = new DefaultProperty();
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

    public function test_lazy_getter_should_create_property(): void
    {
        $a = new LazyProperty();
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

    public function test_should_have_properties(): void
    {
        $a = new HasPropertyFixture();
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

    public function test_should_have_properties_as_camel(): void
    {
        $a = new HasPropertyFixtureCamel();
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

    public function test_should_not_have_property(): void
    {
        $a = new HasPropertyFixture();
        $this->assertFalse($a->has_property('undefined'));
    }

    public function test_lazy_set_should_be_invoked(): void
    {
        $a = new LazySetProperty();
        $a->property = 'testing';
        $this->assertEquals("TESTING", $a->property);
    }

    public function test_undefined_get_should_give_property_list(): void
    {
        $a = new PropertyList();

        try {
            $a->undefined;

            $this->fail("Expected PropertyNotDefined");
        } catch (PropertyNotDefined $e) {
            $this->assertStringContainsString("available properties: public, protected, private", $e->getMessage());
        }
    }
}
