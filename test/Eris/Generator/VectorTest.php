<?php
namespace Eris\Generator;

class VectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->vectorSize = rand(5, 10);
        $this->size = 10;
        $this->elementGenerator = new Natural(1, 10);
        $this->vectorGenerator = new Vector($this->vectorSize, $this->elementGenerator);
        $this->sum = function($acc, $item) {
            $acc = $acc + $item;
            return $acc;
        };
    }

    public function testGeneratesVectorWithGivenSizeAndElementsFromGivenGenerator()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size);

        $this->assertSame($this->vectorSize, count($vector));
        foreach ($vector as $element) {
            $this->assertTrue($this->elementGenerator->contains($element));
        }
    }

    public function testShrinksElementsOfTheVector()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size);

        $previousSum = array_reduce($vector, $this->sum);
        for ($i = 0; $i < 15; $i++) {
            $vector = $generator->shrink($vector);
            $currentSum = array_reduce($vector, $this->sum);
            $this->assertLessThanOrEqual($previousSum, $currentSum);
            $previousSum = $currentSum;
        }
    }

    public function testEachGeneratedVectorShouldBeContainedIntoTheDomain()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size);

        $this->assertTrue($this->vectorGenerator->contains($vector));
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $this->vectorGenerator->shrink("twenty");
    }
}
