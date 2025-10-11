<?php

use Eris\Generators;

class FrequencyTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testFalsyValues(): void
    {
        $this
            ->forAll(
                Generators::frequency(
                    [8, false],
                    [4, 0],
                    [4, '']
                )
            )
            ->then(function ($falsyValue): void {
                $this->assertFalse((bool) $falsyValue);
            });
    }

    public function testAlwaysFails(): void
    {
        $this
            ->forAll(
                Generators::frequency(
                    [8, Generators::choose(1, 100)],
                    [4, Generators::choose(100, 200)],
                    [4, Generators::choose(200, 300)]
                )
            )
            ->then(function ($element): void {
                $this->assertEquals(0, $element);
            });
    }
}
