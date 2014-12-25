# Eris

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/giorgiosironi/eris?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Eris is a porting of [QuickCheck](https://github.com/giorgiosironi/eris-example) and property-based testing tools to the PHP and PHPUnit ecosystem.

In property-based testing, several properties that the System Under Test must respect are defined, and a large sample of generated inputs is sent to it in an attempt to break the properties.

## Installation

You can install Eris through [Composer](https://getcomposer.org/) by adding:

```json
    "require": {
        "giorgiosironi/eris": "dev-master"
    }
```

to your composer.json, and run `composer install`.

You can run some of Eris example tests with `vendor/bin/phpunit vendor/giorgiosironi/eris/examples`.

Here is an [empty sample project](https://github.com/giorgiosironi/eris-example) installing Eris.

Please note the project is in alpha stage and the API may change at any time.

## Example usage within PHPUnit

This test tries to verify that all natural numbers are greater than 42. It's a failing test designed to show you an example of error message.

```php
<?php
use Eris\Generator;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testNaturalNumbersMagnitude()
    {
        $this->forAll([
            Generator\nat(1000),
        ])
            ->then(function($number) {
                $this->assertTrue(
                    $number < 42,
                    "$number is not less than 42 apparently"
                );
            });
    }
}
```

Eris generates a sample of elements from the required domain (here the integers from 0 to plus infinity) and verifies a property on each of them, stopping at the first failure.

```bash
[10:34:32][giorgio@Bipbip:~/code/eris]$ vendor/bin/phpunit examples/ReadmeTest.php
PHPUnit 4.3.5 by Sebastian Bergmann.

Configuration read from /home/giorgio/code/eris/phpunit.xml

F

Time: 234 ms, Memory: 3.25Mb

There was 1 failure:

1) ReadmeTest::testNaturalNumbersMagnitude
42 is not less than 42 apparently
Failed asserting that false is true.

/home/giorgio/code/eris/examples/ReadmeTest.php:15
/home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:48
/home/giorgio/code/eris/src/Eris/Quantifier/RoundRobinShrinking.php:45
/home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:69
/home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:50
/home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:71
/home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:87
/home/giorgio/code/eris/examples/ReadmeTest.php:16
/home/giorgio/code/eris/examples/ReadmeTest.php:16

FAILURES!
Tests: 1, Assertions: 826, Failures: 1.
```

Eris also tries to shrink the input after a failure, giving you the simplest input that still fails the test. In this example, the original input was probably something like `562`, but Eris tries to make it smaller until the test became green again. The smallest value that still fails the test is the one presented to you.

## ChangeLog

Consult [the ChangeLog file](https://github.com/giorgiosironi/eris/blob/master/ChangeLog.md) to know the latest new features.

## Support

Feel free to open issues on the [GitHub project](https://github.com/giorgiosironi/eris/issues) for support.
