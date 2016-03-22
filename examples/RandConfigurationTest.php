<?php
use Eris\Generator;
use Eris\TestTrait;

class RandConfigurationTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testUsingTheDefaultRandFunction()
    {
        $this
            ->withRand('rand')
            ->forAll(
                Generator\int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    public function testUsingTheMtRandFunction()
    {
        $this
            ->withRand('mt_rand')
            ->forAll(
                Generator\int()
            )
            ->then($this->isInteger());
    }

    private function isInteger()
    {
        return function($number) {
            $this->assertInternalType('integer', $number);
        };
    }
}
