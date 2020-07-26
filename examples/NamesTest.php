<?php
use Eris\Generator\NamesGenerator;

class NamesTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testGeneratingNames()
    {
        $this->forAll(
            NamesGenerator::names()
        )->then(function ($name) {
            $this->assertInternalType('string', $name);
            var_dump($name);
        });
    }

    public function testSamplingShrinkingOfNames()
    {
        $generator = NamesGenerator::defaultDataSet();
        $sample = $this->sampleShrink($generator);
        $this->assertInternalType('array', $sample->collected());
        var_dump($sample->collected());
    }
}
