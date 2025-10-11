<?php

use Eris\Generators;
use Eris\Listeners;
use Eris\TestTrait;

class CollectTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testGeneratedDataCollectionOnScalars(): void
    {
        $this
            ->forAll(Generators::neg())
            ->hook(Listeners::collectFrequencies())
            ->then(function ($x): void {
                $this->assertTrue($x < $x + 1);
            });
    }

    public function testGeneratedDataCollectionOnMoreComplexDataStructures(): void
    {
        $this
            ->forAll(
                Generators::vector(2, Generators::int()),
                Generators::char()
            )
            ->hook(Listeners::collectFrequencies())
            ->then(function ($vector): void {
                $this->assertEquals(2, count($vector));
            });
    }

    public function testGeneratedDataCollectionWithCustomMapper(): void
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->withMaxSize(10)
            ->hook(Listeners::collectFrequencies(fn ($array): int => count($array)))
            ->then(function ($array): void {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }
}
