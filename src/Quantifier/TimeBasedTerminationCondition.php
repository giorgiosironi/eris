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

    public function __construct(callable $time, private readonly DateInterval $maximumInterval)
    {
        $this->time = $time;
    }

    public function startPropertyVerification(): void
    {
        $this->limitTime = $this
            ->currentDateTime()
            ->add($this->maximumInterval);
    }

    public function shouldTerminate(): bool
    {
        return $this->currentDateTime() >= $this->limitTime;
    }

    private function currentDateTime(): \DateTime
    {
        return new DateTime("@" . call_user_func($this->time));
    }
}
