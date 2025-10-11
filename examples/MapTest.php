<?php

use Eris\Generators;

class MapTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testApplyingAFunctionToGeneratedValues(): void
    {
        $this->forAll(
            Generators::vector(
                3,
                Generators::map(
                    fn($n): int|float => $n * 2,
                    Generators::nat()
                )
            )
        )
            ->then(function ($tripleOfEvenNumbers): void {
                foreach ($tripleOfEvenNumbers as $number) {
                    $this->assertTrue(
                        $number % 2 === 0,
                        "The element of the vector $number is not even"
                    );
                }
            });
    }

    public function testShrinkingJustMappedValues(): void
    {
        $this->forAll(
            Generators::map(
                fn($n): int|float => $n * 2,
                Generators::nat()
            )
        )
            ->then(function ($evenNumber): void {
                $this->assertLessThanOrEqual(
                    100,
                    $evenNumber,
                    "The number is not less than 100"
                );
            });
    }

    public function testShrinkingMappedValuesInsideOtherGenerators(): void
    {
        $this->forAll(
            Generators::vector(
                3,
                Generators::map(
                    fn($n): int|float => $n * 2,
                    Generators::nat()
                )
            )
        )
            ->then(function ($tripleOfEvenNumbers): void {
                $this->assertLessThanOrEqual(
                    100,
                    array_sum($tripleOfEvenNumbers),
                    "The triple sum " . var_export($tripleOfEvenNumbers, true) . " is not less than 100"
                );
            });
    }

    // TODO: multiple generators means multiple values passed to map
}
