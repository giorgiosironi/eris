<?php

use Eris\Generators;
use Eris\Random;
use Eris\TestTrait;

class RandConfigurationTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testUsingTheDefaultRandFunction()
    {
        $this
            ->withRand('rand')
            ->forAll(
                Generators::int()
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
                Generators::int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    public function testUsingTheDefaultMtRandFunction()
    {
        $this
            ->withRand('mt_rand')
            ->forAll(
                Generators::int()
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
                Generators::int()
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
                Generators::int()
            )
            ->then($this->isInteger());
    }

    private function isInteger()
    {
        return function ($number) {
            \Eris\PHPUnitDeprecationHelper::assertIsInt($number);
        };
    }
}
