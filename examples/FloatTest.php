<?php
use Eris\Generator;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testAPropertyHoldingForAllNumbers()
    {
        $this->forAll(
            Generator\float(-100.0, 100.0)
        )
            ->then(function($number) {
                $this->assertEquals(
                    0.0,
                    $number - $number
                );
            });
    }

    public function testAPropertyHoldingOnlyForPositiveNumbers()
    {
        $this->forAll(
            Generator\float(-10.0, 100.0)
        )
            ->then(function($number) {
                $this->assertTrue(
                    $number >= 0,
                    "$number is not a (loosely) positive number"
                );
            });
    }
}
