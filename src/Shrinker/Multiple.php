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
        $onBadShrink = function ($elementsAfterShrink) use (&$exception, &$branches) {
            var_Dump("bad shrink: " . var_export($elementsAfterShrink->unbox(), true));
            //throw $exception;
        };

        $onGoodShrink = function ($elementsAfterShrink, $exceptionAfterShrink) use (&$elements, &$exception, &$branches) {
            var_Dump("good shrink: " . var_export($elementsAfterShrink->unbox(), true));
            $branches = [];
            $elements = $elementsAfterShrink;
            $exception = $exceptionAfterShrink;
            // see TODO: duplication
            $elementsAfterShrink = $this->generator->shrink($elements);
            if ($elementsAfterShrink instanceof GeneratedValueOptions) {
                foreach ($elementsAfterShrink as $each) {
                    $branches[] = $each;
                }
            } else {
                $branches[] = $elementsAfterShrink;
            }
            var_dump("Branches to look into: " . count($branches));
        };

        // TODO: duplication with cycle
            $elementsAfterShrink = $this->generator->shrink($elements);
            if ($elementsAfterShrink instanceof GeneratedValueOptions) {
                foreach ($elementsAfterShrink as $each) {
                    $branches[] = $each;
                }
            } else {
                $branches[] = $elementsAfterShrink;
            }

        while ($elementsAfterShrink = array_shift($branches)) {
            vaR_dump(count($branches));

            if ($elementsAfterShrink == $elements) {
                $onBadShrink($elements);
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
            throw $exception;
    }
}
