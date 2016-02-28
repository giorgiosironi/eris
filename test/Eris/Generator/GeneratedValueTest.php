<?php
namespace Eris\Generator;

class GeneratedValueTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeMappedOver()
    {
        $initialValue = GeneratedValue::fromJustValue(
            3,
            'my-generator'
        );
        $this->assertEquals(
            GeneratedValue::fromValueAndInput(
                6,
                $initialValue,
                'derived-generator'
            ),
            $initialValue->map(
                function($value) {
                    return $value * 2;
                },
                'derived-generator'
            )
        );
    }

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
