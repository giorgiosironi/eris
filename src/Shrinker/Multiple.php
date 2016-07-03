<?php
namespace Eris\Shrinker;

use Eris\Generator\GeneratedValue;
use Eris\Generator\TupleGenerator;
use Eris\Quantifier\Evaluation;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

class Multiple
{
    public function __construct(array $generators, callable $assertion)
    {
        $this->generator = new TupleGenerator($generators);
        $this->assertion = $assertion;
    }

    public function addGoodShrinkCondition()
    {
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
            throw $exception;
        };

        $onGoodShrink = function ($elementsAfterShrink, $exceptionAfterShrink) use (&$elements, &$exception) {
            $elements = $elementsAfterShrink;
            $exception = $exceptionAfterShrink;
        };

        while (true) {
            $elementsAfterShrink = $this->generator->shrink($elements);

            if ($elementsAfterShrink instanceof GeneratedValueOptions) {
                $elementsAfterShrink = $elementsAfterShrink->first();
            }

            if ($elementsAfterShrink == $elements) {
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
    }
}
