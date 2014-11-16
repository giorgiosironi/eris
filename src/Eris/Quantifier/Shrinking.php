<?php
namespace Eris\Quantifier;
use PHPUnit_Framework_AssertionFailedError;

/**
 * TODO: change namespace. To what?
 */
class Shrinking
{
    private $generators;
    private $assertion;
    private $generatorToShrink = 0;
    
    public function __construct(array $generators, callable $assertion)
    {
        $this->generators = $generators;
        $this->assertion = $assertion;
    }

    /**
     * Precondition: $values should fail $this->assertion
     */
    public function from(array $values, PHPUnit_Framework_AssertionFailedError $exception)
    {
        $smallestValues = $values;
        $smallestException = $exception;
        $this->lastTriedValues = $values;

        while ($newValues = $this->shrink()) {
            $break = null;
            if ($newValues === $this->lastTriedValues) {
                throw $smallestException;
            }
            $this->lastTriedValues = $newValues;
            Evaluation::of($this->assertion)
                ->with($newValues)
                ->onFailure(function($newValues, $e) use (&$smallestValues, &$smallestException, &$break) {
                    $smallestValues = $newValues;
                    $smallestException = $e;
                    $break = false;
                })
                ->onSuccess(function() use (&$break) {
                    $break = true;
                })
                ->execute();
            if ($break) {
                break;
            }
        }
        throw $smallestException;
    }

    private function shrink()
    {
        $values = $this->lastTriedValues;
        $newFirstValue = array_values($this->generators)[$this->generatorToShrink]->shrink();
        $firstKey = array_keys($values)[$this->generatorToShrink];
        $values[$firstKey] = $newFirstValue;
        $this->generatorToShrink++;
        if ($this->generatorToShrink == count($this->generators)) {
            $this->generatorToShrink = 0;
        }
        return $values;
    }
}
