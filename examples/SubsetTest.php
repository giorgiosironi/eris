<?php

use Eris\Generators;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;

class SubsetTest extends TestCase
{
    use TestTrait;

    public function testSubsetsOfASet()
    {
        $this->forAll(
            Generators::subset([
                2, 4, 6, 8, 10
            ])
        )
            ->then(function ($set) {
                \Eris\PHPUnitDeprecationHelper::assertIsArray($set);
                foreach ($set as $element) {
                    $this->assertTrue($this->isEven($element), "Element $element is not even, where did it come from?");
                }
                var_dump($set);
            });
    }

    private function isEven($number)
    {
        return $number % 2 == 0;
    }
}
