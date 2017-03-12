<?php
namespace Eris\Antecedent;

use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit\Framework\ExpectationFailedException;
use Eris\Antecedent;

class IndependentConstraintsAntecedent implements Antecedent
{
    private $constraints;
    
    public static function fromAll($constraints)
    {
        return new self($constraints);
    }

    private function __construct($constraints)
    {
        $this->constraints = $constraints;
    }
    
    public function evaluate(array $values)
    {
        for ($i = 0; $i < count($this->constraints); $i++) {
            // TODO: use Evaluation object?
            try {
                $this->constraints[$i]->evaluate($values[$i]);
            } catch (PHPUnit_Framework_ExpectationFailedException $e) {
                return false;
            } catch (ExpectationFailedException $e) {
                return false;
            }
        }
        return true;
    }
}
