<?php

use Eris\Generator\IntegerGenerator;
use Eris\TestTrait;

class LimitToTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * @eris-repeat 5
     */
    public function testNumberOfIterationsCanBeConfigured()
    {
        $this->forAll(
            IntegerGenerator::int()
        )
            ->then(function ($value) {
                $this->assertInternalType('integer', $value);
            });
    }

    /*
     * future feature
    public function testTimeIntervalToRunForCanBeConfiguredButItNeedsToProduceAtLeastHalfOfTheIterationsByDefault()
    {
        $this->minimum(10)
             ->limitTo(new DateInterval("PT2S"))
             ->forAll(
                IntegerGenerator::int()
            )
            ->then(function($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }
     */

    public function testTimeIntervalToRunForCanBeConfiguredAndAVeryLowNumberOfIterationsCanBeIgnored()
    {
        $this
            ->minimumEvaluationRatio(0)
            ->limitTo(new DateInterval('PT2S'))
            ->forAll(
                IntegerGenerator::int()
            )
            ->then(function ($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }

    /**
     * @eris-ratio 0
     * @eris-duration PT2S
     */
    public function testTimeIntervalToRunForCanBeConfiguredAndAVeryLowNumberOfIterationsCanBeIgnoredFromAnnotation()
    {
        $this->forAll(
            IntegerGenerator::int()
        )
            ->then(function ($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }
}
