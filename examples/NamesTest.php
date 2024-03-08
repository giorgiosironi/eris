<?php

use Eris\Generator;
use Eris\Generators;

class NamesTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testGeneratingNames()
    {
        $this->forAll(
            Generators::names()
        )->then(function ($name) {
            static::assertIsString($name);
            var_dump($name);
        });
    }

    public function testSamplingShrinkingOfNames()
    {
        $generator = Generator\NamesGenerator::defaultDataSet();
        $sample = $this->sampleShrink($generator);
        static::assertIsArray($sample->collected());
        var_dump($sample->collected());
    }
}
