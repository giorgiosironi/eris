<?php
namespace Eris\Shrinker;

use Eris\Generator\GeneratedValue;
use Eris\Generator\GeneratedValueOptions;
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

        $shrink($elements);

        while ($elementsAfterShrink = array_shift($branches)) {
            // TODO: maybe not necessary
            // when Generator start returning emtpy options instead of the 
            // element itself upon no shrinking
            // For now leave in for BC
            if ($elementsAfterShrink == $elements) {
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
}
