<?php
namespace Eris\Shrinker;

use Eris\Generator\GeneratedValueSingle;
use Eris\Generator\GeneratedValueOptions;
use Eris\Generator\TupleGenerator;
use Eris\Quantifier\Evaluation;
use Eris\Shrinker;

class Multiple implements Shrinker
{
    private $generator;
    private $assertion;
    private $goodShrinkConditions = [];
    private $onAttempt = [];

    public function __construct(array $generators, callable $assertion)
    {
        $this->generator = new TupleGenerator($generators);
        $this->assertion = $assertion;
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
    public function from(GeneratedValueSingle $elements, $exception)
    {
        $branches = [];

        $shrink = function ($elements) use (&$elementsAfterShrink, &$branches) {
            $branches = [];
            $elementsAfterShrink = $this->generator->shrink($elements);
            if ($elementsAfterShrink instanceof GeneratedValueOptions) {
                foreach ($elementsAfterShrink as $each) {
                    $branches[] = $each;
                }
            } else {
                $branches[] = $elementsAfterShrink;
            }
            return $branches;
        };

        $onGoodShrink = function ($elementsAfterShrink, $exceptionAfterShrink) use (&$elements, &$exception, &$branches, $shrink) {
            $elements = $elementsAfterShrink;
            $exception = $exceptionAfterShrink;
            $branches = $shrink($elements);
        };

        $this->timeLimit->start();
        $shrink($elements);
        while ($elementsAfterShrink = array_shift($branches)) {
            if ($this->timeLimit->hasBeenReached()) {
                throw new \RuntimeException(
                    "Eris has reached the time limit for shrinking ($this->timeLimit), here it is presenting the simplest failure case." . PHP_EOL
                    . "If you can afford to spend more time to find a simpler failing input, increase it with \$this->shrinkingTimeLimit(\$seconds).",
                    -1,
                    $exception
                );
            }
            // TODO: maybe not necessary
            // when Generator start returning emtpy options instead of the
            // element itself upon no shrinking
            // For now leave in for BC
            if ($elementsAfterShrink == $elements) {
                continue;
            }

            if (!$this->checkGoodShrinkConditions($elementsAfterShrink)) {
                continue;
            }

            foreach ($this->onAttempt as $onAttempt) {
                $onAttempt($elementsAfterShrink);
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure($onGoodShrink)
                ->execute();
        }

        throw $exception;
    }

    private function checkGoodShrinkConditions(GeneratedValueSingle $values)
    {
        foreach ($this->goodShrinkConditions as $condition) {
            if (!$condition($values)) {
                return false;
            }
        }
        return true;
    }
}
