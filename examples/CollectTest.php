<?php

use Eris\Generator\CharacterGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\VectorGenerator;
use Eris\TestTrait;
use Eris\Listener;

class CollectTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testGeneratedDataCollectionOnScalars()
    {
        $this
            ->forAll(IntegerGenerator::neg())
            ->hook(Listener\collectFrequencies())
            ->then(function ($x) {
                $this->assertTrue($x < $x + 1);
            });
    }

    public function testGeneratedDataCollectionOnMoreComplexDataStructures()
    {
        $this
            ->forAll(
                VectorGenerator::vector(2, IntegerGenerator::int()),
                CharacterGenerator::char()
            )
            ->hook(Listener\collectFrequencies())
            ->then(function ($vector) {
                $this->assertEquals(2, count($vector));
            });
    }

    public function testGeneratedDataCollectionWithCustomMapper()
    {
        $this
            ->forAll(
                SequenceGenerator::seq(IntegerGenerator::nat())
            )
            ->withMaxSize(10)
            ->hook(Listener\collectFrequencies(function ($array) {
                return count($array);
            }))
            ->then(function ($array) {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }
}
