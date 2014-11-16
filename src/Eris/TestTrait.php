<?php
namespace Eris;

trait TestTrait
{
    protected $iterations = 100;

    protected function forAll($generators)
    {
        return new Quantifier\ForAll($generators, $this->iterations, $this);
    }

    protected function genNat()
    {
        return new Generator\Natural();
    }
}
