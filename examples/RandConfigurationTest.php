<?php

use Eris\Generator\IntegerGenerator;
use Eris\Random;
use Eris\TestTrait;

class RandConfigurationTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testUsingTheDefaultRandFunction()
    {
        $this
            ->withRand('rand')
            ->forAll(
                IntegerGenerator::int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    /**
     * @eris-method rand
     */
    public function testUsingTheDefaultRandFunctionFromAnnotation()
    {
        $this
            ->forAll(
                IntegerGenerator::int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    public function testUsingTheDefaultMtRandFunction()
    {
        $this
            ->withRand('mt_rand')
            ->forAll(
                IntegerGenerator::int()
            )
            ->then($this->isInteger());
    }


    /**
     * @eris-method mt_rand
     */
    public function testUsingTheDefaultMtRandFunctionFromAnnotation()
    {
        $this
            ->forAll(
                IntegerGenerator::int()
            )
            ->then($this->isInteger());
    }

    public function testUsingThePurePhpMtRandFunction()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('MersenneTwister class does not support HHVM');
        }

        $this
            ->withRand(Random\purePhpMtRand())
            ->forAll(
                IntegerGenerator::int()
            )
            ->then($this->isInteger());
    }

    private function isInteger()
    {
        return function ($number) {
            $this->assertInternalType('integer', $number);
        };
    }
}
