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

use ICanBoogie\Accessor\SerializableTraitTest\DateTimeProperty;
use ICanBoogie\Accessor\SerializableTraitTest\InheritedMixedProperties;
use ICanBoogie\Accessor\SerializableTraitTest\InheritedMixedPropertiesCamel;
use ICanBoogie\Accessor\SerializableTraitTest\PropertyWithGetter;
use ICanBoogie\Accessor\SerializableTraitTest\PropertyWithGetterAndForcedExport;
use ICanBoogie\Accessor\SerializableTraitTest\VirtualProperty;
use PHPUnit\Framework\TestCase;

use const DATE_RFC3339_EXTENDED;

class SerializableTraitTest extends TestCase
{
    public function test_sleep(): void
    {
        $a = new InheritedMixedProperties();
        $properties = $a->__sleep();
        $this->assertArrayHasKey('public', $properties);
        $this->assertArrayNotHasKey('public_with_lazy_getter', $properties);
        $this->assertArrayHasKey('protected', $properties);
        $this->assertArrayHasKey('protected_with_getter', $properties);
        $this->assertArrayHasKey('protected_with_setter', $properties);
        $this->assertArrayHasKey('protected_with_getter_and_setter', $properties);
        $this->assertArrayNotHasKey('protected_with_lazy_getter', $properties);
        $this->assertArrayNotHasKey('private', $properties);
        $this->assertArrayNotHasKey('private_with_getter', $properties);
        $this->assertArrayNotHasKey('private_with_setter', $properties);
        $this->assertArrayHasKey('private_with_getter_and_setter', $properties);
        $this->assertArrayNotHasKey('private_with_lazy_getter', $properties);
    }

    public function test_sleep_camel(): void
    {
        $a = new InheritedMixedPropertiesCamel();
        $properties = $a->__sleep();
        $this->assertArrayHasKey('public', $properties);
        $this->assertArrayNotHasKey('publicWithLazyGetter', $properties);
        $this->assertArrayHasKey('protected', $properties);
        $this->assertArrayHasKey('protectedWithGetter', $properties);
        $this->assertArrayHasKey('protectedWithSetter', $properties);
        $this->assertArrayHasKey('protectedWithGetterAndSetter', $properties);
        $this->assertArrayNotHasKey('protectedWithLazyGetter', $properties);
        $this->assertArrayNotHasKey('private', $properties);
        $this->assertArrayNotHasKey('privateWithGetter', $properties);
        $this->assertArrayNotHasKey('privateWithSetter', $properties);
        $this->assertArrayHasKey('privateWithGetterAndSetter', $properties);
        $this->assertArrayNotHasKey('privateWithLazyGetter', $properties);
    }

    public function test_virtual_properties_should_not_be_exported(): void
    {
        $a = new VirtualProperty();

        $a->minutes = 1;
        $this->assertEquals(1, $a->minutes);
        $this->assertEquals(60, $a->seconds);

        $a->seconds = 120;
        $this->assertEquals(2, $a->minutes);

        $a->minutes *= 2;
        $this->assertEquals(240, $a->seconds);
        $this->assertEquals(4, $a->minutes);

        $this->assertArrayNotHasKey('minutes', (array) $a);
        $this->assertArrayNotHasKey('minutes', $a->__sleep());
    }

    public function test_serialize(): void
    {
        $a = new DateTimeProperty();
        $a->datetime = new \DateTime();
        $b = unserialize(serialize($a));

        $this->assertEquals(
            $a->datetime->format(DATE_RFC3339_EXTENDED),
            $b->datetime->format(DATE_RFC3339_EXTENDED)
        );
    }

    public function test_should_discard_property_with_getter_during_sleep(): void
    {
        $a = new PropertyWithGetter();
        $serialized = serialize($a);
        $this->assertStringNotContainsString("property", $serialized);
    }

    public function test_should_discard_property_with_getter_during_wakeup(): void
    {
        $a = new PropertyWithGetterAndForcedExport();
        $serialized = serialize($a);
        $this->assertStringContainsString("value", $serialized);
        $unserialized = unserialize($serialized);
        $this->assertArrayNotHasKey('property', (array) $unserialized);
    }
}
