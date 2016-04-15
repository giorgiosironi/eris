<?php
namespace Eris\Shrinker;

use Eris\Generator\GeneratedValue;
use Eris\Generator\TupleGenerator;
use Eris\Quantifier\Evaluation;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

class Random // implements Shrinker
{
    private $generator;
    private $assertion;
    private $attempts;
    private $goodShrinkConditions = [];
    private $onAttempt = [];

    public function __construct(array $generators, callable $assertion)
    {
        $this->generator = new TupleGenerator($generators);
        $this->assertion = $assertion;
        $this->attempts = new Attempts($giveUpAfter = 100);
        $this->timeLimit = new NoTimeLimit();
    }

    public function setTimeLimit(TimeLimit $timeLimit)
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }

    public function addGoodShrinkCondition(callable $condition)
    {
        $this->goodShrinkConditions[] = $condition;
        return $this;
    }

    public function onAttempt(callable $listener)
    {
        $this->onAttempt[] = $listener;
        return $this;
    }

    /**
     * Precondition: $values should fail $this->assertion
     */
    public function from(GeneratedValue $elements, AssertionFailed $exception)
    {
        $onBadShrink = function () use (&$exception) {
            $this->attempts->increase();
            $this->attempts->ensureLimit($exception);
        };

        $onGoodShrink = function ($elementsAfterShrink, $exceptionAfterShrink) use (&$elements, &$exception) {
            $this->attempts->reset();
            $elements = $elementsAfterShrink;
            $exception = $exceptionAfterShrink;
        };

        $this->timeLimit->start();
        while (!$this->timeLimit->hasBeenReached()) {
            $elementsAfterShrink = $this->generator->shrink($elements);

            if ($elementsAfterShrink == $elements) {
                $onBadShrink();
                continue;
            }

            if (!$this->checkGoodShrinkConditions($elementsAfterShrink)) {
                $onBadShrink();
                continue;
            }

            foreach ($this->onAttempt as $onAttempt) {
                $onAttempt($elementsAfterShrink);
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure($onGoodShrink)
                ->onSuccess($onBadShrink)
                ->execute();
        }

        throw new \RuntimeException(
            "Eris has reached the time limit for shrinking ($this->timeLimit), here it is presenting the simplest failure case." . PHP_EOL
            . "If you can afford to spend more time to find a simpler failing input, increase it with \$this->shrinkingTimeLimit(\$seconds).",
            -1,
            $exception
        );
    }

    private function checkGoodShrinkConditions(GeneratedValue $values)
    {
        foreach ($this->goodShrinkConditions as $condition) {
            if (!$condition($values)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * @private
 * Do not use outside of this file.
 */
class Attempts
{
    private $total = 0;
    private $giveUpAfter;
    
    public function __construct($giveUpAfter)
    {
        $this->giveUpAfter = $giveUpAfter;
    }

    public function increase()
    {
        $this->total++;
    }

    public function reset()
    {
        $this->total = 0;
    }

    public function ensureLimit(\Exception $exception)
    {
        if ($this->total >= $this->giveUpAfter) {
            throw $exception;
        }
    }
}
