<?php

use Eris\Generators;
use Eris\TestTrait;

class DisableShrinkingTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    /**
     * @var int
     */
    private $calls;

    /**
     * Shrinking may be avoided when then() is slow or non-deterministic.
     */
    public function testThenIsNotCalledMultipleTime()
    {
        $this->calls = 0;
        $this
            ->forAll(
                Generators::nat()
            )
            ->disableShrinking()
            ->then(function ($number) {
                $this->calls++;
                $this->assertTrue(false, "Total calls: {$this->calls}");
            });
    }
}
