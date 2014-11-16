<?php
namespace Quantifier;

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

    public function from(array $values)
    {
        $smallestValues = $values;
        $smallestException = null;
        Evaluation::of($this->assertion)
            ->with($values)
            ->onFailure(function($e) use (&$smallestException) {
                $smallestException = $e;
            })
            ->execute();
        $this->lastTriedValues = $values;

        while ($newValues = $this->shrink()) {
            $break = null;
            $this->lastTriedValues = $newValues;
            Evaluation::of($this->assertion)
                ->with($newValues)
                ->onFailure(function($e, $newValues) use (&$smallestValues, &$smallestException, &$break) {
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
        var_Dump($values);
        return $values;
    }
}
