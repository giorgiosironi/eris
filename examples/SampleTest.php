<?php
use Eris\Generator;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSamplingValues()
    {
        $generator = Generator\nat(1000);
        var_dump($this->sample($generator));
    }

    public function testSamplingShrinking()
    {
        $generator = Generator\nat(1000);
        var_dump($this->sampleShrink($generator));
    }
}
