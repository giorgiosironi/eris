<?php
namespace Eris\Generator;

class VectorGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->vectorSize = rand(5, 10);
        $this->size = 10;
        $this->elementGenerator = new ChooseGenerator(1, 10);
        $this->vectorGenerator = new VectorGenerator($this->vectorSize, $this->elementGenerator);
        $this->sum = function($acc, $item) {
            $acc = $acc + $item;
            return $acc;
        };
        $this->rand = 'rand';
    }

    public function testGeneratesVectorWithGivenSizeAndElementsFromGivenGenerator()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size, $this->rand);

        $this->assertSame($this->vectorSize, count($vector->unbox()));
        foreach ($vector->unbox() as $element) {
            $this->assertTrue($this->elementGenerator->contains(
                GeneratedValue::fromJustValue($element)
            ));
        }
    }

    public function testShrinksElementsOfTheVector()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size, $this->rand);

        $previousSum = array_reduce($vector->unbox(), $this->sum);
        for ($i = 0; $i < 15; $i++) {
            $vector = $generator->shrink($vector);
            $currentSum = array_reduce($vector->unbox(), $this->sum);
            $this->assertLessThanOrEqual($previousSum, $currentSum);
            $previousSum = $currentSum;
        }
    }

    public function testEachGeneratedVectorShouldBeContainedIntoTheDomain()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size, $this->rand);

        $this->assertTrue($this->vectorGenerator->contains($vector));
    }
}
