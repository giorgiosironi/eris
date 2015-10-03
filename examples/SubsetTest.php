<?php
use Eris\Generator;
use Eris\TestTrait;

class SubsetTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSubsetsOfAnotherGeneratorsDomain()
    {
        $this->forAll(
            Generator\subset(Generator\nat())
        )
            ->then(function($set) {
                $this->assertInternalType('array', $set);
                foreach ($set as $element) {
                    $this->assertGreaterThanOrEqual(0, $element);
                }
            });
    }
}
