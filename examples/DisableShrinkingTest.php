<?php

use Eris\Generators;
use Eris\TestTrait;

class DisableShrinkingTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    private ?int $calls = null;

    /**
     * Shrinking may be avoided when then() is slow or non-deterministic.
     */
    public function testThenIsNotCalledMultipleTime(): void
    {
        $this->calls = 0;
        $this
            ->forAll(
                Generators::nat()
            )
            ->disableShrinking()
            ->then(function ($number): void {
                $this->calls++;
                $this->assertTrue(false, "Total calls: {$this->calls}");
            });
    }
}
