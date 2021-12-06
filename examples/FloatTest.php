<?php

use Eris\Generators;

class FloatTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testAPropertyHoldingForAllNumbers()
    {
        $this->forAll(Generators::float())
            ->then(function ($number) {
                $this->assertEquals(
                    0.0,
                    abs($number) - abs($number)
                );
            });
    }

    public function testAPropertyHoldingOnlyForPositiveNumbers()
    {
        $this->forAll(Generators::float())
            ->then(function ($number) {
                $this->assertTrue(
                    $number >= 0,
                    "$number is not a (loosely) positive number"
                );
            });
    }
}
