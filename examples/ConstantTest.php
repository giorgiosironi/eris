<?php

use Eris\Generator;

class ConstantTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testUseConstantGeneratorExplicitly()
    {
        $this
            ->forAll(
                Generator\nat(),
                Generator\constant(2)
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }

    public function testUseConstantGeneratorImplicitly()
    {
        $this
            ->forAll(
                Generator\nat(),
                2
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }
}
