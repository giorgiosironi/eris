<?php
namespace Eris\Antecedent;

use Eris\Antecedent;
use PHPUnit\Framework\ExpectationFailedException;

class IndependentConstraintsAntecedent implements Antecedent
{
    public static function fromAll($constraints): self
    {
        return new self($constraints);
    }

    private function __construct(private $constraints)
    {
    }
    
    public function evaluate(array $values): bool
    {
        for ($i = 0, $iMax = count($this->constraints); $i < $iMax; $i++) {
            // TODO: use Evaluation object?
            try {
                $this->constraints[$i]->evaluate($values[$i]);
            } catch (ExpectationFailedException) {
                return false;
            }
        }
        return true;
    }
}
