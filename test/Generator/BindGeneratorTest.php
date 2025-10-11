<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class BindGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesAGeneratedValueObject(): void
    {
        $generator = new BindGenerator(
            // TODO: order of parameters should be consistent with map, or not?
            ConstantGenerator::box(4),
            fn ($n): \Eris\Generator\ChooseGenerator => new ChooseGenerator($n, $n + 10)
        );
        self::assertIsInt(
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOuterGenerator(): void
    {
        $generator = new BindGenerator(
            new ChooseGenerator(0, 5),
            fn ($n): \Eris\Generator\ChooseGenerator => new ChooseGenerator($n, $n + 10)
        );
        $value = $generator->__invoke($this->size, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            self::assertIsInt(
                $value->unbox()
            );
            $value = $generator->shrink($value);
        }
        $this->assertLessThanOrEqual(5, $value->unbox());
    }

    public function testAssociativeProperty(): void
    {
        $firstGenerator = new BindGenerator(
            new BindGenerator(
                new ChooseGenerator(0, 5),
                fn ($n): \Eris\Generator\ChooseGenerator => new ChooseGenerator($n * 10, $n * 10 + 1)
            ),
            fn ($m): \Eris\Generator\VectorGenerator => new VectorGenerator($m, new IntegerGenerator())
        );
        $secondGenerator = new BindGenerator(
            new ChooseGenerator(0, 5),
            fn ($n): \Eris\Generator\BindGenerator => new BindGenerator(
                new ChooseGenerator($n * 10, $n * 10 + 1),
                fn ($m): \Eris\Generator\VectorGenerator => new VectorGenerator($m, new IntegerGenerator())
            )
        );
        for ($i = 0; $i < 100; $i++) {
            $this->assertIsAnArrayOfX0OrX1Elements($firstGenerator->__invoke($this->size, $this->rand)->unbox());
            $this->assertIsAnArrayOfX0OrX1Elements($secondGenerator->__invoke($this->size, $this->rand)->unbox());
        }
    }

    public function testShrinkBindGeneratorWithCompositeValue(): void
    {
        $bindGenerator = new BindGenerator(
            new ChooseGenerator(0, 5),
            fn ($n): \Eris\Generator\TupleGenerator => new TupleGenerator([$n])
        );
        $generatedValue = $bindGenerator->__invoke($this->size, $this->rand);
        $firstShrunkValue = $bindGenerator->shrink($generatedValue);
        $secondShrunkValue = $bindGenerator->shrink($firstShrunkValue);
        $this->assertInstanceOf(GeneratedValue::class, $secondShrunkValue);
    }

    private function assertIsAnArrayOfX0OrX1Elements(array $value): void
    {
        $this->assertContains(
            count($value) % 10,
            [0, 1],
            "The array has " . count($value) . " elements"
        );
    }
}
