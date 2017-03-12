<?php
namespace Eris\Generator;

class GeneratedValueTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeMappedToDeriveValues()
    {
        $initialValue = GeneratedValueSingle::fromJustValue(
            3,
            'my-generator'
        );
        $this->assertEquals(
            GeneratedValueSingle::fromValueAndInput(
                6,
                $initialValue,
                'derived-generator'
            ),
            $initialValue->map(
                function ($value) {
                    return $value * 2;
                },
                'derived-generator'
            )
        );
    }

    public function testDerivedValueCanBeAnnotatedWithNewGeneratorNameWithoutBeingChanged()
    {
        $initialValue = GeneratedValueSingle::fromJustValue(
            3,
            'my-generator'
        );
        $this->assertEquals(
            GeneratedValueSingle::fromValueAndInput(
                3,
                $initialValue,
                'derived-generator'
            ),
            $initialValue->derivedIn('derived-generator')
        );
    }

    public function testCanBeRepresentedOnOutput()
    {
        $generatedValue = GeneratedValueSingle::fromValueAndInput(
            422,
            GeneratedValueSingle::fromJustValue(211),
            'map'
        );
        $this->assertInternalType('string', $generatedValue->__toString());
        $this->assertRegExp('/value.*422/', $generatedValue->__toString());
        $this->assertRegExp('/211/', $generatedValue->__toString());
        $this->assertRegExp('/generator.*map/', $generatedValue->__toString());
    }

    public function testCanBeIteratedUponAsASingleOption()
    {
        $generatedValue = GeneratedValueSingle::fromValueAndInput(
            422,
            GeneratedValueSingle::fromJustValue(211),
            'map'
        );
        $this->assertEquals([$generatedValue], iterator_to_array($generatedValue));
    }
}
