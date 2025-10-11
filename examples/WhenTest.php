<?php

use Eris\Generators;

class WhenTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testWhenWithAnAnonymousFunctionWithGherkinSyntax(): void
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->when(fn($n): bool => $n > 42)
            ->then(function ($number): void {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithAnAnonymousFunctionForMultipleArguments(): void
    {
        $this->forAll(
            Generators::choose(0, 1000),
            Generators::choose(0, 1000)
        )
            ->when(fn($first, $second): bool => $first > 42 && $second > 23)
            ->then(function ($first, $second): void {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testWhenWithOnePHPUnitConstraint(): void
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->when($this->greaterThan(42))
            ->then(function ($number): void {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithMultiplePHPUnitConstraints(): void
    {
        $this->forAll(
            Generators::choose(0, 1000),
            Generators::choose(0, 1000)
        )
            ->when($this->greaterThan(42), $this->greaterThan(23))
            ->then(function ($first, $second): void {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testMultipleWhenClausesWithGherkinSyntax(): void
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->when($this->greaterThan(42))
            ->and($this->lessThan(900))
            ->then(function ($number): void {
                $this->assertTrue(
                    $number > 42 && $number < 900,
                    "\$number was filtered to be between 42 and 900, but it is $number"
                );
            });
    }

    public function testWhenWhichSkipsTooManyValues(): void
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->when($this->greaterThan(800))
            ->then(function ($number): void {
                $this->assertTrue(
                    $number > 800
                );
            });
    }

    /**
     * The current implementation shows no problem as PHPUnit prefers to show
     * the exception from the test method than the one from teardown
     * when both fail.
     */
    public function testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne(): void
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->when($this->greaterThan(100))
            ->then(function ($number): void {
                $this->assertTrue(
                    $number <= 100,
                    "\$number should be less or equal to 100, but it is $number"
                );
            });
    }

    public function testSizeIncreasesEvenIfEvaluationsAreSkippedDueToAntecedentsNotBeingSatisfied(): void
    {
        $this->forAll(
            Generators::seq(Generators::elements(1, 2, 3))
        )
            ->when(fn($seq): bool => count($seq) > 0)
            ->then(function ($seq): void {
                $this->assertGreaterThan(0, count($seq));
            });
    }
}
