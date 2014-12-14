<?php
use Eris\Generator;

class NamesTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSamplingShrinkingOfNames()
    {
        $generator = Generator\Names::defaultDataSet();
        var_dump($this->sampleShrink($generator)->collected());
    }
}
