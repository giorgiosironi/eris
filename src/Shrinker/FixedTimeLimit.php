<?php
namespace Eris\Shrinker;

class FixedTimeLimit implements TimeLimit
{
    private $maximumIntervalLength;
    private $clock;
    private $startOfTheInterval;

    public static function realTime($maximumIntervalLength)
    {
        return new self($maximumIntervalLength, 'time');
    }
    
    /**
     * @param int $maximumIntervalLength  in seconds
     */
    public function __construct($maximumIntervalLength, callable $clock)
    {
        $this->maximumIntervalLength = $maximumIntervalLength;
        $this->clock = $clock;
    }

    public function start(): void
    {
        $this->startOfTheInterval = call_user_func($this->clock);
    }

    public function hasBeenReached(): bool
    {
        $actualIntervalLength = call_user_func($this->clock) - $this->startOfTheInterval;
        return $actualIntervalLength >= $this->maximumIntervalLength;
    }

    public function __toString(): string
    {
        $actualIntervalLength = call_user_func($this->clock) - $this->startOfTheInterval;
        return "{$actualIntervalLength}s elapsed of {$this->maximumIntervalLength}s";
    }
}
