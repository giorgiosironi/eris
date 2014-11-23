<?php

/**
 * Some of these would make good unit tests, but importing them
 * doesn't solve the problem as the more important ones are the failures
 * We need to look into end-to-end testing
 */
class SuchThatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSuchThatWithAnAnonymousFunctionForASingleArgument()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat(function($n) {
                return $n > 42;
            })        
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testSuchThatWithAnAnonymousFunctionForMultipleArguments()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->suchThat(function($first, $second) {
                return $first > 42 && $second > 23;
            })        
            ->__invoke(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testSuchThatWithOnePHPUnitConstraint()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(42))
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testSuchThatWithMultiplePHPUnitConstraints()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(42), $this->greaterThan(23))
            ->__invoke(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testMultipleSuchThatClauses()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(42))
            ->suchThat($this->lessThan(900))
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 42 && $number < 900,
                    "\$number was filtered to be between 42 and 900, but it is $number"
                );
            });
    }

    public function testSuchThatWhichSkipsTooManyValues()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(800))
            ->__invoke(function($number) {
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
    public function testSuchThatFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(100))
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number <= 100,
                    "\$number should be less or equal to 100, but it is $number"
                );
            });
    }
}
