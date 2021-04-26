<?php
use Eris\Generator\IntegerGenerator;
use Eris\Generator\OneOfGenerator;

class OneOfTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testPositiveOrNegativeNumberButNotZero()
    {
        $this
            ->forAll(
                OneOfGenerator::oneOf(
                    IntegerGenerator::pos(),
                    IntegerGenerator::neg()
                )
            )
            ->then(function ($number) {
                $this->assertNotEquals(0, $number);
            });
    }
}
