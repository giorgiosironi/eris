<?php
namespace Quantifier;

class Shrinking
{
    private $generators;
    private $assertion;
    
    public function __construct(array $generators, callable $assertion)
    {
        $this->generators = $generators;
        $this->assertion = $assertion;
    }

    public function from(array $values)
    {
        $smallestValues = $values;
        try {
            call_user_func_array($this->assertion, $values);
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $smallestException = $e;
        }

        while ($newValues = $this->shrink()) {
            try {
                call_user_func_array($this->assertion, $newValues);
                break;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                $smallestValues = $newValues;
                $smallestException = $e;
                continue;
            }
        }
        throw $smallestException;

    }

    private function shrink()
    {
        array_values($this->generators)[0]->shrink();
        foreach ($this->generators as $name => $generator) {
            $values[] = $generator();
        }
        return $values;
    }
}
