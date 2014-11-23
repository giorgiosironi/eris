<?php
namespace Eris\Quantifier;

interface Antecedent
{
    /**
     * @param array $values  all the values in a single shot
     * @return boolean
     */
    public function evaluate(array $values);
}
