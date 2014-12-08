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

    // TODO: vector of oneOf
}
