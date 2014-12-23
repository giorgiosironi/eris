<?php
use Eris\Generator;
use Eris\TestTrait;

class ShrinkingTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;   

    public function testShrinkingRespectsAntecedents()
    {
        $this->forAll([
                Generator\int(0, 100),
            ])
            ->when(function($number) {
                return $number > 10;
            })
            ->then(function($number) {
                $this->assertTrue($number % 3 == 0, "The number $number is not multiple of 3");
            });
    }
}
