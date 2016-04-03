<?php
use Eris\Generator;
use Eris\TestTrait;
use Eris\Listener;

class CollectTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testGeneratedDataCollectionOnScalars()
    {
        $this
            ->forAll(Generator\neg())
            ->hook(Listener\collectFrequencies())
            ->then(function ($x) {
                $this->assertTrue($x < $x + 1);
            });
    }

    public function testGeneratedDataCollectionOnMoreComplexDataStructures()
    {
        $this
            ->forAll(
                Generator\vector(2, Generator\int()),
                Generator\char()
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
                Generator\seq(Generator\nat())
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
