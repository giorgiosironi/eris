<?php

use Eris\Generators;
use Eris\TestTrait;

class ShrinkingTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testShrinkingAString()
    {
        $this->forAll(
            Generators::string()
        )
            ->then(function ($string) {
                var_dump($string);
                \Eris\PHPUnitDeprecationHelper::assertStringNotContainsString('B', $string);
            });
    }

    public function testShrinkingRespectsAntecedents()
    {
        $this->forAll(
            Generators::choose(0, 20)
        )
            ->when(function ($number) {
                return $number > 10;
            })
            ->then(function ($number) {
                $this->assertTrue($number % 29 == 0, "The number $number is not multiple of 29");
            });
    }
}
