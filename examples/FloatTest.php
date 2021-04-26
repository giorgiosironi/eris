<?php
use Eris\Generator\FloatGenerator;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testAPropertyHoldingForAllNumbers()
    {
        $this->forAll(FloatGenerator::float())
            ->then(function ($number) {
                $this->assertEquals(
                    0.0,
                    abs($number) - abs($number)
                );
            });
    }

    public function testAPropertyHoldingOnlyForPositiveNumbers()
    {
        $this->forAll(FloatGenerator::float())
            ->then(function ($number) {
                $this->assertTrue(
                    $number >= 0,
                    "$number is not a (loosely) positive number"
                );
            });
    }
}
