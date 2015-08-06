<?php
use Eris\Generator;
use Eris\TestTrait;

class ShrinkingTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testShrinkingRespectsAntecedents()
    {
        $this->markTestSkipped('Easy to refactor this test with Generator\choose(0, 20)');
        $this->forAll([
                Generator\int(0, 20),
            ])
            ->when(function($number) {
                return $number > 10;
            })
            ->then(function($number) {
                $this->assertTrue($number % 29 == 0, "The number $number is not multiple of 29");
            });
    }
}
