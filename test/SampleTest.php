<?php

namespace Eris;

use Eris\Generator\IntegerGenerator;
use Eris\Generator\SuchThatGenerator;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;
    
    public function testWithGeneratorSize()
    {
        $times         = 100;
        $generatorSize = 100;
        $generator     = SuchThatGenerator::suchThat(function ($n) {
            return $n > 10;
        }, IntegerGenerator::nat());
        $sample        = $this->sample($generator, $times, $generatorSize);
        $this->assertNotEmpty(count($sample->collected()));
    }
}
