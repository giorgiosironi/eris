<?php

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    private $iterations = 100;

    protected function forAll($generators)
    {
        return new Quantifier\ForAll($generators, $this->iterations, $this);
    }

    protected function genVector($elementGenerator)
    {
        return new Generator\Vector($elementGenerator);
    } 

    protected function genInt()
    {
        return new Generator\Integer();
    }
}
