<?php
namespace Eris\Generator;

class GeneratedValueTest extends \PHPUnit\Framework\TestCase
{
    public function testCanBeMappedToDeriveValues(): void
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

    public function testDerivedValueCanBeAnnotatedWithNewGeneratorNameWithoutBeingChanged(): void
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

    public function testCanBeRepresentedOnOutput(): void
    {
        $generatedValue = GeneratedValueSingle::fromValueAndInput(
            422,
            GeneratedValueSingle::fromJustValue(211),
            'map'
        );
        \Eris\PHPUnitDeprecationHelper::assertIsString($generatedValue->__toString());
        \Eris\PHPUnitDeprecationHelper::assertMatchesRegularExpression('/value.*422/', $generatedValue->__toString());
        \Eris\PHPUnitDeprecationHelper::assertMatchesRegularExpression('/211/', $generatedValue->__toString());
        \Eris\PHPUnitDeprecationHelper::assertMatchesRegularExpression('/generator.*map/', $generatedValue->__toString());
    }

    public function testCanBeIteratedUponAsASingleOption(): void
    {
        $generatedValue = GeneratedValueSingle::fromValueAndInput(
            422,
            GeneratedValueSingle::fromJustValue(211),
            'map'
        );
        $this->assertEquals([$generatedValue], iterator_to_array($generatedValue));
    }
}
