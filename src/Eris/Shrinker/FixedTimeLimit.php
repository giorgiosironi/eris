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

    public function start()
    {
        $this->startOfTheInterval = call_user_func($this->clock);
    }

    public function hasBeenReached()
    {
        $actualIntervalLength = call_user_func($this->clock) - $this->startOfTheInterval;
        return $actualIntervalLength >= $this->maximumIntervalLength;
    }
}
