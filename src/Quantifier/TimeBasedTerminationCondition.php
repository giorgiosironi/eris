<?php
namespace Eris\Quantifier;

use Eris\Listener;
use Eris\Listener\EmptyListener;
use DateTime;
use DateInterval;

class TimeBasedTerminationCondition extends EmptyListener implements TerminationCondition, Listener
{
    private $limitTime;
    private $time;
    private $maximumInterval;

    public function __construct(callable $time, DateInterval $maximumInterval)
    {
        $this->time = $time;
        $this->maximumInterval = $maximumInterval;
    }

    public function startPropertyVerification()
    {
        $this->limitTime = $this
            ->currentDateTime()
            ->add($this->maximumInterval);
    }

    public function shouldTerminate()
    {
        return $this->currentDateTime() >= $this->limitTime;
    }

    private function currentDateTime()
    {
        return new DateTime("@" . call_user_func($this->time));
    }
}
