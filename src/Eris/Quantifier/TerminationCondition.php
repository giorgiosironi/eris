<?php
namespace Eris\Quantifier;

interface TerminationCondition
{
    /**
     * @return boolean
     */
    public function shouldTerminate();
}
