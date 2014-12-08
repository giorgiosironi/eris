<?php
use Eris\BaseTestCase;

class OneOfTest extends BaseTestCase
{
    public function testOneOfOnlyProducesElementsFromTheGivenArguments()
    {
        $this->forAll([
            $this->genOneOf(1, 2, 3),
        ])
            ->__invoke(function($number) {
                $this->assertContains(
                    $number, 
                    [1, 2, 3]
                );
            });
    }

    /**
     * This means you cannot have a oneOf Generator with a single element,
     * which is perfectly fine as if you have a single
     * element this generator is useless.
     */
    public function testOneOfOnlyProducesElementsFromTheGivenArrayDomain()
    {
        $this->forAll([
            $this->genOneOf([1, 2, 3]),
        ])
            ->__invoke(function($number) {
                $this->assertContains(
                    $number, 
                    [1, 2, 3]
                );
            });
    }


    public function testVectorOfOneOfGenerators()
    {
        $this->markTestSkipped("We have not wired the genVector method yet?");
        $this->forAll([
            $this->genVector(
                $this->genOneOf([2, 4, 6, 8, 10, 12])
            )
        ])
            ->__invoke(function($vector) {
                $sum = array_sum($vector);
                $isEven = function($number) { return $mumber % 2 == 0; };
                $this->assertTrue(
                    $isEven($sum), 
                    "$sum is not even, but it's the sum of the vector " . var_export($vector, true)
                );
            });
    }
}
