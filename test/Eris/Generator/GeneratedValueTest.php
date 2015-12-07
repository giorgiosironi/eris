<?php
namespace Eris\Generator;

class GeneratedValueTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeRepresentedOnOutput()
    {
        $generatedValue = GeneratedValue::fromValueAndInput(
            422,
            GeneratedValue::fromJustValue(211),
            'map'
        );
        $this->assertInternalType('string', $generatedValue->__toString());
        $this->assertRegexp('/value.*422/', $generatedValue->__toString());
        $this->assertRegexp('/211/', $generatedValue->__toString());
        $this->assertRegexp('/generator.*map/', $generatedValue->__toString());
    }
}
