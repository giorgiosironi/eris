<?php
namespace Eris\Shrinker;

class FixedTimeLimitTest extends \PHPUnit_Framework_TestCase
{
    public function testDetectsAMaximumTimeHasElapsed()
    {
        $this->time = 1000000000;
        $limit = new FixedTimeLimit(
            30,
            function () {
                return $this->time;
            }
        );
        $limit->start();

        $this->assertFalse($limit->hasBeenReached(), "Limit should not be immediately reached");

        $this->time = 1000000029;
        $this->assertFalse($limit->hasBeenReached(), "Limit reached too soon");

        $this->time = 1000000030;
        $this->assertTrue($limit->hasBeenReached(), "Limit not reached yet");
    }
}
