<?php

use Eris\Attributes\ErisRatio;
use Eris\Generators;

class MinimumEvaluationsTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testFailsBecauseOfTheLowEvaluationRatio(): void
    {
        $this
            ->forAll(
                Generators::choose(0, 100)
            )
            ->when(fn($n): bool => $n > 90)
            ->then(function ($number): void {
                $this->assertTrue($number * 2 > 90 * 2);
            });
    }

    public function testPassesBecauseOfTheArtificiallyLowMinimumEvaluationRatio(): void
    {
        $this
            ->minimumEvaluationRatio(0.01)
            ->forAll(
                Generators::choose(0, 100)
            )
            ->when(fn($n): bool => $n > 90)
            ->then(function ($number): void {
                $this->assertTrue($number * 2 > 90 * 2);
            });
    }

    #[ErisRatio(ratio: 1)]
    public function testPassesBecauseOfTheArtificiallyLowMinimumEvaluationRatioFromAnnotation(): void
    {
        $this
            ->forAll(
                Generators::choose(0, 100)
            )
            ->when(fn($n): bool => $n > 90)
            ->then(function ($number): void {
                $this->assertTrue($number * 2 > 90 * 2);
            });
    }
}
