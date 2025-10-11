<?php
namespace Eris\Quantifier;

use DateInterval;

class TimeBasedTerminationConditionTest extends \PHPUnit\Framework\TestCase
{
    private \Closure $time;
    private int $currentTime;
    private \Eris\Quantifier\TimeBasedTerminationCondition $condition;

    protected function setUp(): void
    {
        $this->time = (fn() => $this->currentTime);
        $this->currentTime = 1300000000;
        $this->condition = new TimeBasedTerminationCondition(
            $this->time,
            new DateInterval('PT1800S')
        );
    }
    
    public function testDefaultsToNotTerminateAtStartup(): void
    {
        $this->condition->startPropertyVerification();
        $this->assertFalse(
            $this->condition->shouldTerminate()
        );
    }

    public function testWhenAnIntervalShorterThanTheMaximumIntervalIsElapsedChoosesNotToTerminate(): void
    {
        $this->condition->startPropertyVerification();
        $this->currentTime = 1300001000;
        $this->assertFalse(
            $this->condition->shouldTerminate()
        );
    }

    public function testWhenTheMaximumIntervalIsElapsedChoosesToTerminate(): void
    {
        $this->condition->startPropertyVerification();
        $this->currentTime = 1300002000;
        $this->assertTrue(
            $this->condition->shouldTerminate()
        );
    }
}
