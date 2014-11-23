<?php
namespace Eris\Quantifier;
use PHPUnit_Framework_Constraint_Callback;
use PHPUnit_Framework_ExpectationFailedException;

class Antecedent
{
    private $constraints;
    
    public static function fromConstraints($constraints)
    {
        return new self($constraints);
    }

    public static function fromCallback($callback)
    {
        return new self([new PHPUnit_Framework_Constraint_Callback($callback)]);
    }
    
    private function __construct($constraints)
    {
        $this->constraints = $constraints;
    }
    
    public function evaluate(array $values)
    {
        for ($i = 0; $i < count($this->constraints); $i++) {
            try {
                $this->constraints[$i]->evaluate($values[$i]);
            } catch (PHPUnit_Framework_ExpectationFailedException $e) {
                return false;
            }
        }
        return true;
    }

}
