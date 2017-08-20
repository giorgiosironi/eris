<?php
use Eris\Generator;

class ElementsTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testElementsOnlyProducesElementsFromTheGivenArguments()
    {
        $this->forAll(
            Generator\elements(1, 2, 3)
        )
            ->then(function ($number) {
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
    public function testElementsOnlyProducesElementsFromTheGivenArrayDomain()
    {
        $this->forAll(
            Generator\elements([1, 2, 3])
        )
            ->then(function ($number) {
                $this->assertContains(
                    $number,
                    [1, 2, 3]
                );
            });
    }


    public function testVectorOfElementsGenerators()
    {
        $this->forAll(
            Generator\vector(
                4,
                Generator\elements([2, 4, 6, 8, 10, 12])
            )
        )
            ->then(function ($vector) {
                $sum = array_sum($vector);
                $isEven = function ($number) {
                    return $number % 2 == 0;
                };
                $this->assertTrue(
                    $isEven($sum),
                    "$sum is not even, but it's the sum of the vector " . var_export($vector, true)
                );
            });
    }
}
