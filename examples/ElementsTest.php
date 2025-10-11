<?php

use Eris\Generators;

class ElementsTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testElementsOnlyProducesElementsFromTheGivenArguments(): void
    {
        $this->forAll(
            Generators::elements(1, 2, 3)
        )
            ->then(function ($number): void {
                $this->assertContains(
                    $number,
                    [1, 2, 3]
                );
            });
    }

    /**
     * This means you cannot have a Elements Generator with a single element,
     * which is perfectly fine as if you have a single element this generator
     * is useless. Use Constant Generator instead
     */
    public function testElementsOnlyProducesElementsFromTheGivenArrayDomain(): void
    {
        $this->forAll(
            Generators::elements([1, 2, 3])
        )
            ->then(function ($number): void {
                $this->assertContains(
                    $number,
                    [1, 2, 3]
                );
            });
    }


    public function testVectorOfElementsGenerators(): void
    {
        $this->forAll(
            Generators::vector(
                4,
                Generators::elements([2, 4, 6, 8, 10, 12])
            )
        )
            ->then(function ($vector): void {
                $sum = array_sum($vector);
                $isEven = (fn($number): bool => $number % 2 === 0);
                $this->assertTrue(
                    $isEven($sum),
                    "$sum is not even, but it's the sum of the vector " . var_export($vector, true)
                );
            });
    }
}
