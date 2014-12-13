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
        $this->giveUpAfter = 100;
    }

    /**
     * Precondition: $values should fail $this->assertion
     */
    public function from(array $elements, AssertionFailed $exception)
    {
        $attempts = 0;

        while ($elementsAfterShrink = $this->generator->shrink($elements)) {
            if ($elementsAfterShrink === $elements) {
                $attempts += 1;
                if ($attempts >= $this->giveUpAfter) {
                    throw $exception;
                }
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure(
                    function($elementsAfterShrink, $exceptionAfterShrink)
                        use (&$elements, &$exception, &$attempts) {
                        if ($elements !== $elementsAfterShrink) {
                            $attempts = 0;
                        }
                        $elements = $elementsAfterShrink;
                        $exception = $exceptionAfterShrink;
                    }
                )
                ->onSuccess(function() use ($exception, &$attempts) {
                    $attempts += 1;
                    if ($attempts >= $this->giveUpAfter) {
                        throw $exception;
                    }
                })
                ->execute();
        }
        throw $exception;
    }
}
