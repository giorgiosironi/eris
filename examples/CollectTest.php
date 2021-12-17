<?php

use Eris\Generators;
use Eris\Listeners;
use Eris\TestTrait;

class CollectTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testGeneratedDataCollectionOnScalars()
    {
        $this
            ->forAll(Generators::neg())
            ->hook(Listeners::collectFrequencies())
            ->then(function ($x) {
                $this->assertTrue($x < $x + 1);
            });
    }

    public function testGeneratedDataCollectionOnMoreComplexDataStructures()
    {
        $this
            ->forAll(
                Generators::vector(2, Generators::int()),
                Generators::char()
            )
            ->hook(Listeners::collectFrequencies())
            ->then(function ($vector) {
                $this->assertEquals(2, count($vector));
            });
    }

    public function testGeneratedDataCollectionWithCustomMapper()
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->withMaxSize(10)
            ->hook(Listeners::collectFrequencies(function ($array) {
                return count($array);
            }))
            ->then(function ($array) {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }
}
