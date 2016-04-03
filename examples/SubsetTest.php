<?php
use Eris\Generator;
use Eris\TestTrait;

class SubsetTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSubsetsOfASet()
    {
        $this->forAll(
            Generator\subset([
                2, 4, 6, 8, 10
            ])
        )
            ->then(function ($set) {
                $this->assertInternalType('array', $set);
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
