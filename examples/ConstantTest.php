<?php


use Eris\Generator\ConstantGenerator;
use Eris\Generator\IntegerGenerator;

class ConstantTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testUseConstantGeneratorExplicitly()
    {
        $this
            ->forAll(
                IntegerGenerator::nat(),
                ConstantGenerator::constant(2)
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }

    public function testUseConstantGeneratorImplicitly()
    {
        $this
            ->forAll(
                IntegerGenerator::nat(),
                2
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }
}
