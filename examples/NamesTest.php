<?php

use Eris\Generator;
use Eris\Generators;

class NamesTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testGeneratingNames(): void
    {
        $this->forAll(
            Generators::names()
        )->then(function ($name): void {
            self::assertIsString($name);
            var_dump($name);
        });
    }

    public function testSamplingShrinkingOfNames(): void
    {
        $generator = Generator\NamesGenerator::defaultDataSet();
        $sample = $this->sampleShrink($generator);
        self::assertIsArray($sample->collected());
        var_dump($sample->collected());
    }
}
