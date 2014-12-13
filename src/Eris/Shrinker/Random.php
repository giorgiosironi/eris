<?php
namespace Eris\Shrinker;
use Eris\Generator\Tuple;
use Eris\Quantifier\Evaluation;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

class Random // implements Shrinker
{
    private $generator;
    private $assertion;
    private $giveUpAfter;

    public function __construct(array $generators, callable $assertion)
    {
        $this->generator = new Tuple($generators);
        $this->assertion = $assertion;
        $this->attempts = new Attempts($giveUpAfter = 100);
    }

    /**
     * Precondition: $values should fail $this->assertion
     */
    public function from(array $elements, AssertionFailed $exception)
    {
        $attemptFailed = function() use (&$exception) {
            $this->attempts->increase();
            $this->attempts->ensureLimit($exception);
        };

        while ($elementsAfterShrink = $this->generator->shrink($elements)) {
            if ($elementsAfterShrink === $elements) {
                $attemptFailed();
                continue;
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure(
                    function($elementsAfterShrink, $exceptionAfterShrink)
                        use (&$elements, &$exception) {
                        if ($elements !== $elementsAfterShrink) {
                            $this->attempts->reset();
                        }
                        $elements = $elementsAfterShrink;
                        $exception = $exceptionAfterShrink;
                    }
                )
                ->onSuccess($attemptFailed)
                ->execute();
        }
        throw $exception;
    }
}

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
