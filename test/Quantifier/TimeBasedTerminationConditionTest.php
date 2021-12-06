<?php
namespace Eris\Quantifier;

use DateInterval;

class TimeBasedTerminationConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Closure
     */
    private $time;
    /**
     * @var int
     */
    private $currentTime;
    /**
     * @var TimeBasedTerminationCondition
     */
    private $condition;

    protected function setUp(): void
    {
        $this->time = function () {
            return $this->currentTime;
        };
        $this->currentTime = 1300000000;
        $this->condition = new TimeBasedTerminationCondition(
            $this->time,
            new DateInterval('PT1800S')
        );
    }
    
    public function testDefaultsToNotTerminateAtStartup()
    {
        $this->condition->startPropertyVerification();
        $this->assertFalse(
            $this->condition->shouldTerminate()
        );
    }

    public function testWhenAnIntervalShorterThanTheMaximumIntervalIsElapsedChoosesNotToTerminate()
    {
        $this->condition->startPropertyVerification();
        $this->currentTime = 1300001000;
        $this->assertFalse(
            $this->condition->shouldTerminate()
        );
    }

    public function testWhenTheMaximumIntervalIsElapsedChoosesToTerminate()
    {
        $this->condition->startPropertyVerification();
        $this->currentTime = 1300002000;
        $this->assertTrue(
            $this->condition->shouldTerminate()
        );
    }
}
