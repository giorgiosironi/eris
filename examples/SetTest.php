<?php
use Eris\Generator;
use Eris\TestTrait;

class SetTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSetsOfAnotherGeneratorsDomain()
    {
        $this->forAll(
            Generator\set(Generator\nat())
        )
            ->then(function ($set) {
                $this->assertInternalType('array', $set);
                foreach ($set as $element) {
                    $this->assertGreaterThanOrEqual(0, $element);
                }
            });
    }
}
