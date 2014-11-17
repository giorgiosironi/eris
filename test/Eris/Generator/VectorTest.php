<?php
namespace Eris\Generator;

class NaturalTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->vectorSize = rand(5, 10);
        $this->generator = new Vector($this->vectorSize, new Natural(1, 10));
        $this->sum = function($acc, $item) {
            $acc = $acc + $item;
            return $acc;
        };
    }

    public function testGeneratesVectorWithGivenSizeAndElementsFromGivenGenerator()
    {
        $generator = $this->generator;
        $vector = $generator();

        $this->assertEquals($this->vectorSize, count($vector));
        $elementsSum = array_reduce($vector, $this->sum);
        $this->assertGreaterThanOrEqual(5, $elementsSum);
        $this->assertLessThanOrEqual(100, $elementsSum);
    }

    public function testShrinksElementsOfTheVector()
    {
        $generator = $this->generator;
        $vector = $generator();

        $previousSum = array_reduce($vector, $this->sum);
        for ($i = 0; $i < 15; $i++) {
            $vector = $generator->shrink();
            $currentSum = array_reduce($vector, $this->sum);
            $this->assertLessThanOrEqual($previousSum, $currentSum);
            $previousSum = $currentSum;
        }
    }
}
