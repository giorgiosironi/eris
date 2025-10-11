<?php

use Eris\Attributes\ErisMethod;
use Eris\Generators;
use Eris\Random;
use Eris\TestTrait;

class RandConfigurationTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testUsingTheDefaultRandFunction(): void
    {
        $this
            ->withRand('rand')
            ->forAll(
                Generators::int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    #[ErisMethod(method: 'rand')]
    public function testUsingTheDefaultRandFunctionFromAnnotation(): void
    {
        $this
            ->forAll(
                Generators::int()
            )
            ->withMaxSize(1000 * 1000* 1000)
            ->then($this->isInteger());
    }

    public function testUsingTheDefaultMtRandFunction(): void
    {
        $this
            ->withRand('mt_rand')
            ->forAll(
                Generators::int()
            )
            ->then($this->isInteger());
    }

    #[ErisMethod(method: 'mt_rand')]
    public function testUsingTheDefaultMtRandFunctionFromAnnotation(): void
    {
        $this
            ->forAll(
                Generators::int()
            )
            ->then($this->isInteger());
    }

    public function testUsingThePurePhpMtRandFunction(): void
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
        return function ($number): void {
            self::assertIsInt($number);
        };
    }
}
