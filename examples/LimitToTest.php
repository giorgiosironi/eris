<?php
use Eris\Generator;
use Eris\TestTrait;

class LimitToTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testNumberOfIterationsCanBeConfigured()
    {
        $this->limitTo(5)
             ->forAll(
                Generator\int()
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
                Generator\int()
            )
            ->then(function($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }
     */

    public function testTimeIntervalToRunForCanBeConfiguredAndAVeryLowNumberOfIterationsCanBeIgnored()
    {
        $this->minimumEvaluationRatio(0.0)
             ->limitTo(new DateInterval("PT2S"))
             ->forAll(
                Generator\int()
            )
            ->then(function ($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }
}
