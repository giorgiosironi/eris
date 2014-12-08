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

    // TODO: genOneOf([...])
    // TODO: vector of oneOf
}
