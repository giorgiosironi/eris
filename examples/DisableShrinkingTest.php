<?php

use Eris\Generator\IntegerGenerator;
use Eris\TestTrait;

class DisableShrinkingTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * Shrinking may be avoided when then() is slow or non-deterministic.
     */
    public function testThenIsNotCalledMultipleTime()
    {
        $this->calls = 0;
        $this
            ->forAll(
                IntegerGenerator::nat()
            )
            ->disableShrinking()
            ->then(function ($number) {
                $this->calls++;
                $this->assertTrue(false, "Total calls: {$this->calls}");
            });
    }
}
