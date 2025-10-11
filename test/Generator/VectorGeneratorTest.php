<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class VectorGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $vectorSize;
    private int $size;
    private \Eris\Generator\ChooseGenerator $elementGenerator;
    private \Eris\Generator\VectorGenerator $vectorGenerator;
    private \Closure $sum;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->vectorSize = random_int(5, 10);
        $this->size = 10;
        $this->elementGenerator = new ChooseGenerator(1, 10);
        $this->vectorGenerator = new VectorGenerator($this->vectorSize, $this->elementGenerator);
        $this->sum = (fn ($acc, $item): float|int|array => $acc + $item);
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesVectorWithGivenSizeAndElementsFromGivenGenerator(): void
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

    public function testShrinksElementsOfTheVector(): void
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
