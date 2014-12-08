eris
====

Eris is a porting of Quickcheck and property-based testing tools to the PHP and PHPUnit ecosystem.

## Example usage within PHPUnit

This test tries to verify that all natural numbers are greater than 42. It's a failing test designed to show you an example of error message.

```php
class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testNaturalNumbersMagnitude()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->then(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number is not greater than 42 apparently"
                );
            });
    }
}
```

Eris generates a sample of elements from the required domain (here the integers from 0 to plus infinity) and verifies a property on each of them, stopping at the first failure.

```bash
[10:28:20][giorgio@Bipbip:~/code/eris]$ vendor/bin/phpunit examples/ReadmeTest.php 
PHPUnit 4.3.5 by Sebastian Bergmann.

Configuration read from /home/giorgio/code/eris/phpunit.xml

F

Time: 30 ms, Memory: 3.25Mb

There was 1 failure:

1) ReadmeTest::testNaturalNumbersMagnitude
$number is not greater than 42 apparently
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
Tests: 1, Assertions: 41, Failures: 1.
```

