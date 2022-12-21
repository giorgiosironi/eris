<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class VectorGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $vectorSize;
    /**
     * @var int
     */
    private $size;
    /**
     * @var ChooseGenerator
     */
    private $elementGenerator;
    /**
     * @var VectorGenerator
     */
    private $vectorGenerator;
    /**
     * @var \Closure
     */
    private $sum;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp(): void
    {
        $this->vectorSize = rand(5, 10);
        $this->size = 10;
        $this->elementGenerator = new ChooseGenerator(1, 10);
        $this->vectorGenerator = new VectorGenerator($this->vectorSize, $this->elementGenerator);
        $this->sum = function ($acc, $item) {
            $acc = $acc + $item;
            return $acc;
        };
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesVectorWithGivenSizeAndElementsFromGivenGenerator()
    {
        $generator = $this->vectorGenerator;
        /** @var GeneratedValue $vector */
        $vector = $generator($this->size, $this->rand);
        self::assertInstanceOf(GeneratedValue::class, $vector);

        $this->assertCount($this->vectorSize, $vector->unbox());
        foreach ($vector->unbox() as $element) {
            $this->assertGreaterThanOrEqual(1, $element);
            $this->assertLessThanOrEqual(10, $element);
        }
    }

    public function testShrinksElementsOfTheVector()
    {
        $generator = $this->vectorGenerator;
        $vector = $generator($this->size, $this->rand);

        $previousSum = array_reduce($vector->unbox(), $this->sum);
        for ($i = 0; $i < 15; $i++) {
            $vector = GeneratedValueOptions::mostPessimisticChoice($vector);
            $vector = $generator->shrink($vector);
            $currentSum = array_reduce($vector->unbox(), $this->sum);
            $this->assertLessThanOrEqual($previousSum, $currentSum);
            $previousSum = $currentSum;
        }
    }
}
