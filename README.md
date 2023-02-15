# Accessor

[![Release](https://img.shields.io/packagist/v/icanboogie/accessor.svg)](https://packagist.org/packages/icanboogie/accessor)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Accessor.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Accessor)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Accessor.svg)](https://coveralls.io/r/ICanBoogie/Accessor)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/accessor.svg)](https://packagist.org/packages/icanboogie/accessor)

The **icanboogie/accessor** package allows classes to implement [ICanBoogie][]'s accessor
design pattern. Using a combination of getters, setters, properties, and property visibilities,
you can create read-only properties, write-only properties, virtual properties;
and implement defaults, type control, guarding, and lazy loading.



#### Installation

```bash
composer require icanboogie/inflector
```



### Preamble

Because the package is a citizen of [ICanBoogie][]'s realm, which elected [snake case][] a long
time ago for its readability, the following examples use the same casing, but [CamelCase][] is
equally supported as we'll learn by the end of this document. Actually, because all getters and
setters are formatted using the `accessor_format` trait method it is very easy to bind the
formatting to one's requirements simply by overriding that method.





## Getters and setters

A getter is a method that gets the value of a specific property. A setter is a method that sets
the value of a specific property. You can define getters and setters on classes using
the [AccessorTrait][] trait, and optionally inform of its feature by implementing the
[HasAccessor][] interface.

__Something to remember__: Getters and setters are only invoked when their corresponding property
is not accessible. This is most notably important to remember when using lazy loading,
which creates the associated property when it is invoked.

__Another thing to remember__: You don't _need_ to use getter/setter for everything and their cats,
PHP is no Java, and it's okay to have public properties.





## Read-only properties

Read-only properties are created by defining only a getter. A [PropertyNotWritable][] exception
is thrown in attempt to set a read-only property.

The following example demonstrates how a `property` read-only property can be implemented:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property-read mixed $property
 */
class ReadOnlyProperty
{
    use AccessorTrait;

    protected function get_property()
    {
        return 'value';
    }
}

$a = new ReadOnlyProperty;
echo $a->property;     // value
$a->property = null;   // throws ICanBoogie\PropertyNotWritable
```

An existing property can be made read-only by setting its visibility to `protected` or `private`:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property-read mixed $property
 */
class ReadOnlyProperty
{
    use AccessorTrait;

    private $property = "value";

    protected function get_property()
    {
        return $this->property;
    }
}

$a = new ReadOnlyProperty;
echo $a->property;     // value
$a->property = null;   // throws ICanBoogie\PropertyNotWritable
```

### Protecting a _construct_ property

Read-only properties are often used to provide read access to a property that was provided
during _construct_, which should stay unchanged during the life time of an instance.

The following example demonstrates how a `connection` property passed during _construct_
can only be read afterwards. The visibility of the property is set to _private_
so that even an extending class cannot modify the property.

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

class Connection
{
    // …
}

/**
 * @property-read Connection $connection
 */
class Model
{
    use AccessorTrait;

    /**
     * @var Connection
     */
    private $connection;

    protected function get_connection(): Connection
    {
        return $this->connection;
    }

    protected $options;

    public function __construct(Connection $connection, array $options)
    {
        $this->connection = $connection;
        $this->options = $options;
    }
}

$connection = new Connection(…);
$model = new Model($connection, …);

$connection === $model->connection;   // true
$model->connection = null;            // throws ICanBoogie\PropertyNotWritable
```





## Write-only properties

Write-only properties are created by defining only a setter. A [PropertyNotReadable][] exception
is thrown in attempt to get a write-only property.

The following example demonstrates how a `property` write-only property can be implemented:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property-write mixed $property
 */
class WriteOnlyProperty
{
    use AccessorTrait;

    protected function set_property($value)
    {
        // …
    }
}

$a = new WriteOnlyProperty;
$a->property = 'value';
echo $a->property;   // throws ICanBoogie\PropertyNotReadable
```

An existing property can be made write-only by setting its visibility to `protected` or `private`:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property-write mixed $property
 */
class WriteOnlyProperty
{
    use AccessorTrait;

    private $property = 'value';

    protected function set_property($value)
    {
        $this->property = $value;
    }
}

$a = new WriteOnlyProperty;
$a->property = 'value';
echo $a->property;   // throws ICanBoogie\PropertyNotReadable
```





## Virtual properties

A virtual property is created by defining a getter and a setter but no corresponding property.
Virtual properties are usually providing an interface to another property or data structure.

The following example demonstrates how a `minutes` virtual property can be implemented
as an interface to a `seconds` property.

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property int $minutes
 */
class Time
{
    use AccessorTrait;

    public $seconds;

    protected function set_minutes(int $minutes)
    {
        $this->seconds = $minutes * 60;
    }

    protected function get_minutes(): int
    {
        return $this->seconds / 60;
    }
}

$time = new Time;
$time->seconds = 120;
echo $time->minutes;   // 2

$time->minutes = 4;
echo $time->seconds;   // 240
```





## Providing a default value until a property is set

Because getters are invoked when their corresponding property is inaccessible,
and because an unset property is of course inaccessible, it is possible to define getters
providing default values until a value is actually set.

The following example demonstrates how a default value can be provided while a property
is inaccessible (unset an that case). During construct, if the `slug` property is empty
it is unset, making it inaccessible. Thus, until the property is actually set,
when the `slug` property is read its getter is invoked and returns a default value created from
the `title` property.

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

class Article
{
    use AccessorTrait;

    public $title;
    public $slug;

    public function __construct(string $title, string $slug = null)
    {
        $this->title = $title;

        if ($slug)
        {
            $this->slug = $slug;
        }
        else
        {
            unset($this->slug);
        }
    }

    protected function get_slug(): string
    {
        return \ICanBoogie\normalize($this->title);
    }
}

$article = new Article("This is my article");
echo $article->slug;   // this-is-my-article
$article->slug = "my-article";
echo $article->slug;   // my-article
unset($article->slug);
echo $article->slug;   // this-is-my-article
```





## Façade properties (and type control)

Sometimes you want to be able to manage the type of a property, what can be stored,
what can be retrieved, the most transparently possible. This can be achieved
with _façade properties_.

Façade properties are implemented by defining a private property along with its getter and setter.

The following example demonstrates how a `created_at` property is implemented.
It can be set to a mixed value, but is always read as a `DateTime` instance.

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\DateTime;

/**
 * @property DateTime $created_at
 */
class Article
{
    use AccessorTrait;

    private $created_at;

    protected function set_created_at($datetime)
    {
        $this->created_at = $datetime;
    }

    protected function get_created_at(): DateTime
    {
        $datetime = $this->created_at;

        if ($datetime instanceof DateTime)
        {
            return $datetime;
        }

        return $this->created_at = ($datetime === null) ? DateTime::none() : new DateTime($datetime, 'utc');
    }
}
```





### Façade properties are exported on serialization

Although façade properties are defined using private properties, they are exported when
the instance is serialized, just like they would if they were public or protected.

```php
<?php

$article = new Article;
$article->created_at = 'now';

$test = unserialize(serialize($article));
echo get_class($test->created_at);           // ICanBoogie/DateTime
$article->created_at == $test->created_at;   // true
```





## Lazy loading

Lazy loading creates the associated property when it is invoked, making subsequent accesses using
the property rather than the getter.

In the following example, the `lazy_get_pseudo_uniqid()` getter returns a unique value,
but because the `pseudo_uniqid` property is created with the `public` visibility after
the getter was called, any subsequent access to the property returns the same value:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property string $pseudo_uniqid
 */
class PseudoUniqID
{
    use AccessorTrait;

    protected function lazy_get_pseudo_uniqid(): string
    {
        return uniqid();
    }
}

$a = new PseudoUniqID;

echo $a->pseudo_uniqid; // 5089497a540f8
echo $a->pseudo_uniqid; // 5089497a540f8
```

Of course, unsetting the created property resets the process.

```php
<?php

unset($a->pseudo_uniqid);

echo $a->pseudo_uniqid; // 508949b5aaa00
echo $a->pseudo_uniqid; // 508949b5aaa00
```





### Setting a lazy property

Lazy properties are implemented similarly to read-only properties, by defining a method
to get a value, but unlike read-only properties lazy properties can be written too:

```php
<?php

$a = new PseudoUniqID;

echo $a->pseudo_uniqid;   // a009b3a984a50
$a->pseudo_uniqid = 123456;
echo $a->pseudo_uniqid;   // 123456

unset($a->pseudo_uniqid);
echo $a->pseudo_uniqid;   // 57e5ada092180
```

You need to remember that lazy properties actually _create_ a property,
thus the getter won't be invoked if the property is already accessible.





## Overloading getters and setters

Because getters and setters are classic methods, they can be overloaded. That is,
the setter or getter of a parent class can be overloaded by an extending class.

The following example demonstrates how an `Awesome` class extending an `Plain` class can turn
a _plain_ getter into an awesome getter:

```php
<?php

use ICanBoogie\Accessor\AccessorTrait;

/**
 * @property-read string $property
 */
class Plain
{
    use AccessorTrait;

    protected function get_property()
    {
        return "value";
    }
}

class Awesome extends Plain
{
    protected function get_property()
    {
        return "awesome " . parent::get_property();
    }
}

$plain = new Plain;
echo $plain->property;     // value

$awesome = new Awesome;
echo $awesome->property;   // awesome value
```





## CamelCase support

[CamelCase][] getters and setters are equally supported. Instead of using the [AccessorTrait][],
use the [AccessorCamelTrait][]:

```php
<?php

use ICanBoogie\Accessor\AccessorCamelTrait;

/**
 * @property-read $camelProperty
 */
class CamelExample
{
    use AccessorCamelTrait;

    private $camelProperty;

    protected function getCamelProperty()
    {
        return $this->camelProperty;
    }

    public function __construct($value)
    {
        $this->camelProperty = $value;
    }
}

$a = new CamelExample("value");
echo $a->camelProperty;   // value
```





----------



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/ICanBoogie/Accessor/actions).

[![Tests](https://github.com/ICanBoogie/Accessor/workflows/test/badge.svg)](https://github.com/ICanBoogie/Accessor/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/ICanBoogie/Accessor/workflows/static-analysis/badge.svg)](https://github.com/ICanBoogie/Accessor/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/ICanBoogie/Accessor/workflows/code-style/badge.svg)](https://github.com/ICanBoogie/Accessor/actions?query=workflow%3Acode-style)



## Code of Conduct

This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in
this project and its community, you are expected to uphold this code.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.



## License

**icanboogie/accessor** is released under the [BSD-3-Clause](LICENSE).




[documentation]:       https://icanboogie.org/api/accessor/master/
[AccessorCamelTrait]:  https://icanboogie.org/api/accessor/master/class-ICanBoogie.Accessor.AccessorCamelTrait.html
[AccessorTrait]:       https://icanboogie.org/api/accessor/master/class-ICanBoogie.Accessor.AccessorTrait.html
[FormatAsCamel]:       https://icanboogie.org/api/accessor/master/class-ICanBoogie.Accessor.FormatAsCamel.html
[HasAccessor]:         https://icanboogie.org/api/accessor/master/class-ICanBoogie.Accessor.HasAccessor.html
[PropertyNotWritable]: https://icanboogie.org/api/common/1.2/class-ICanBoogie.PropertyNotWritable.html
[PropertyNotReadable]: https://icanboogie.org/api/common/1.2/class-ICanBoogie.PropertyNotReadable.html
[ICanBoogie]:          https://icanboogie.org
[CamelCase]:           http://en.wikipedia.org/wiki/CamelCase
[Snake case]:          http://en.wikipedia.org/wiki/Snake_case
