<?php

use Eris\Generators;
use Eris\TestTrait;

class ShrinkingTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testShrinkingAString(): void
    {
        $this->forAll(
            Generators::string()
        )
            ->then(function ($string): void {
                var_dump($string);
                self::assertStringNotContainsString('B', $string);
            });
    }

    public function testShrinkingRespectsAntecedents(): void
    {
        $this->forAll(
            Generators::choose(0, 20)
        )
            ->when(fn ($number): bool => $number > 10)
            ->then(function ($number): void {
                $this->assertTrue($number % 29 === 0, "The number $number is not multiple of 29");
            });
    }
}
