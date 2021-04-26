<?php

use Eris\Generator\IntegerGenerator;
use Eris\Generator\SetGenerator;
use Eris\TestTrait;

class SetTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSetsOfAnotherGeneratorsDomain()
    {
        $this->forAll(
            SetGenerator::set(IntegerGenerator::nat())
        )
            ->then(function ($set) {
                $this->assertInternalType('array', $set);
                foreach ($set as $element) {
                    $this->assertGreaterThanOrEqual(0, $element);
                }
            });
    }
}
