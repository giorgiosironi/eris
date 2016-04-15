<?php
use Eris\Generator;
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
                Generator\nat()
            )
            ->disableShrinking()
            ->then(function ($number) {
                $this->calls++;
                $this->assertTrue(false, "Total calls: {$this->calls}");
            });
    }
}
