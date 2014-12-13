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
        $attempts = new Attempts();

        while ($elementsAfterShrink = $this->generator->shrink($elements)) {
            if ($elementsAfterShrink === $elements) {
                $attempts->increase();
                $attempts->ensureLimit($this->giveUpAfter, $exception);
                continue;
            }

            Evaluation::of($this->assertion)
                ->with($elementsAfterShrink)
                ->onFailure(
                    function($elementsAfterShrink, $exceptionAfterShrink)
                        use (&$elements, &$exception, $attempts) {
                        if ($elements !== $elementsAfterShrink) {
                            $attempts->reset();
                        }
                        $elements = $elementsAfterShrink;
                        $exception = $exceptionAfterShrink;
                    }
                )
                ->onSuccess(function() use ($exception, $attempts) {
                    $attempts->increase();
                    $attempts->ensureLimit($this->giveUpAfter, $exception);
                })
                ->execute();
        }
        throw $exception;
    }
}

class Attempts
{
    private $total = 0;

    public function increase()
    {
        $this->total++;
    }

    public function reset()
    {
        $this->total = 0;
    }

    public function ensureLimit($limit, \Exception $exception)
    {
        if ($this->total >= $limit) {
            throw $exception;
        }
    }
}
