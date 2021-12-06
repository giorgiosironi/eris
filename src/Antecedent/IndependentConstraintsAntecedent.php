<?php
namespace Eris\Antecedent;

use Eris\Antecedent;
use PHPUnit\Framework\ExpectationFailedException;

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
        for ($i = 0, $iMax = count($this->constraints); $i < $iMax; $i++) {
            // TODO: use Evaluation object?
            try {
                $this->constraints[$i]->evaluate($values[$i]);
            } catch (ExpectationFailedException $e) {
                return false;
            }
        }
        return true;
    }
}
