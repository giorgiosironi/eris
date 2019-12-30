<?php

namespace Eris;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;
    
    public function testWithGeneratorSize()
    {
        $times         = 100;
        $generatorSize = 100;
        $generator     = Generator\suchThat(function ($n) {
            return $n > 10;
        }, Generator\nat());
        $sample        = $this->sample($generator, $times, $generatorSize);
        $this->assertNotEmpty(count($sample->collected()));
    }
}