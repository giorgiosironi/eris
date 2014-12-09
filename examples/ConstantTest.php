<?php

use Eris\Generator;

class ConstantTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testUseConstantGeneratorExplicitly()
    {
        $this
            ->forAll([
                $this->genNat(),
                new Generator\Constant(2)
            ])
            ->then(function($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }

    public function testUseConstantGeneratorImplicitly()
    {
        $this
            ->forAll([
                $this->genNat(),
                2
            ])
            ->then(function($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }
}
