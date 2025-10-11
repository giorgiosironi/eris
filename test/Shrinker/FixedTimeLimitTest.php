<?php
namespace Eris\Shrinker;

class FixedTimeLimitTest extends \PHPUnit\Framework\TestCase
{
    private ?int $time = null;

    public function testDetectsAMaximumTimeHasElapsed(): void
    {
        $this->time = 1000000000;
        $limit = new FixedTimeLimit(
            30,
            fn (): int => $this->time
        );
        $limit->start();

        $this->assertFalse($limit->hasBeenReached(), "Limit should not be immediately reached");

        $this->time = 1000000029;
        $this->assertFalse($limit->hasBeenReached(), "Limit reached too soon");

        $this->time = 1000000030;
        $this->assertTrue($limit->hasBeenReached(), "Limit not reached yet");
    }
}
