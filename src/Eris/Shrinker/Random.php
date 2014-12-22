<?php
namespace Eris\Shrinker;
use Eris\Generator\Tuple;
use Eris\Quantifier\Evaluation;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

class Random // implements Shrinker
{
    private $generator;
    private $assertion;
    private $attempts;

    public function __construct(array $generators, callable $assertion)
    {
        $this->generator = new Tuple($generators);
        $this->assertion = $assertion;
        $this->attempts = new Attempts($giveUpAfter = 100);
        $this->timeLimit = TimeLimit::realTime(30);
    }

    public function setTimeLimit(TimeLimit $timeLimit)
    {
        $this->timeLimit = $timeLimit; 
    }

    /**
     * Precondition: $values should fail $this->assertion
     */
    public function from(array $elements, AssertionFailed $exception)
    {
        $onBadShrink = function() use (&$exception) {
            $this->attempts->increase();
            $this->attempts->ensureLimit($exception);
        };

        $onGoodShrink = function($elementsAfterShrink, $exceptionAfterShrink) use (&$elements, &$exception) {
            $this->attempts->reset();
            $elements = $elementsAfterShrink;
            $exception = $exceptionAfterShrink;
        };

        $this->timeLimit->start();
        while (!$this->timeLimit->hasBeenReached()) {
            $elementsAfterShrink = $this->generator->shrink($elements);

            if ($elementsAfterShrink === $elements) {
                $onBadShrink();
                continue;
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure($onGoodShrink)
                ->onSuccess($onBadShrink)
                ->execute();
        }
        throw $exception;
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
