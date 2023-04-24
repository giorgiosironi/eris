<?php

use Eris\Attributes\ErisDuration;
use Eris\Attributes\ErisRatio;
use Eris\Attributes\ErisRepeat;
use Eris\Generators;
use Eris\TestTrait;

class LimitToTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    #[ErisRepeat(repeat: 5)]
    public function testNumberOfIterationsCanBeConfigured()
    {
        $this->forAll(
            Generators::int()
        )
            ->then(function ($value) {
                static::assertIsInt($value);
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
        $this
            ->minimumEvaluationRatio(0)
            ->limitTo(new DateInterval('PT2S'))
            ->forAll(
                Generators::int()
            )
            ->then(function ($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }

    #[ErisRatio(ratio: 0)]
    #[ErisDuration(duration: 'PT2S')]
    public function testTimeIntervalToRunForCanBeConfiguredAndAVeryLowNumberOfIterationsCanBeIgnoredFromAnnotation()
    {
        $this->forAll(
            Generators::int()
        )
            ->then(function ($value) {
                usleep(100 * 1000);
                $this->assertTrue(true);
            });
    }
}
