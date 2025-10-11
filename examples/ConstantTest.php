<?php

use Eris\Generators;

class ConstantTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testUseConstantGeneratorExplicitly(): void
    {
        $this
            ->forAll(
                Generators::nat(),
                Generators::constant(2)
            )
            ->then(function ($number, $alwaysTwo): void {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }

    public function testUseConstantGeneratorImplicitly(): void
    {
        $this
            ->forAll(
                Generators::nat(),
                2
            )
            ->then(function ($number, $alwaysTwo): void {
                $this->assertTrue(($number * $alwaysTwo % 2) === 0);
            });
    }
}
