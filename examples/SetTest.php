<?php

use Eris\Generators;
use Eris\TestTrait;

class SetTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testSetsOfAnotherGeneratorsDomain()
    {
        $this->forAll(
            Generators::set(Generators::nat())
        )
            ->then(function ($set) {
                $this->assertInternalType('array', $set);
                foreach ($set as $element) {
                    $this->assertGreaterThanOrEqual(0, $element);
                }
            });
    }
}
