<?php

use Eris\Generators;

class ConstantTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testUseConstantGeneratorExplicitly()
    {
        $this
            ->forAll(
                Generators::nat(),
                Generators::constant(2)
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }

    public function testUseConstantGeneratorImplicitly()
    {
        $this
            ->forAll(
                Generators::nat(),
                2
            )
            ->then(function ($number, $alwaysTwo) {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }
}
