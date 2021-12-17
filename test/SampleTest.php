<?php

namespace Eris;

class SampleTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;
    
    public function testWithGeneratorSize()
    {
        $times         = 100;
        $generatorSize = 100;
        $generator     = Generators::suchThat(function ($n) {
            return $n > 10;
        }, Generators::nat());
        $sample        = $this->sample($generator, $times, $generatorSize);
        $this->assertNotEmpty(count($sample->collected()));
    }
}
