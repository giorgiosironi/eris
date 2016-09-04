<?php
use Eris\Generator;

class MapTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testApplyingAFunctionToGeneratedValues()
    {
        $this->forAll(
            Generator\vector(
                3,
                Generator\map(
                    function ($n) {
                        return $n * 2;
                    },
                    Generator\nat()
                )
            )
        )
            ->then(function ($tripleOfEvenNumbers) {
                foreach ($tripleOfEvenNumbers as $number) {
                    $this->assertTrue(
                        $number % 2 == 0,
                        "The element of the vector $number is not even"
                    );
                }
            });
    }

    public function testShrinkingJustMappedValues()
    {
        $this->forAll(
            Generator\map(
                function ($n) {
                    return $n * 2;
                },
                Generator\nat()
            )
        )
            ->then(function ($evenNumber) {
                $this->assertLessThanOrEqual(
                    100,
                    $evenNumber,
                    "The number is not less than 100"
                );
            });
    }

    public function testShrinkingMappedValuesInsideOtherGenerators()
    {
        $this->forAll(
            Generator\vector(
                3,
                Generator\map(
                    function ($n) {
                        return $n * 2;
                    },
                    Generator\nat()
                )
            )
        )
            ->then(function ($tripleOfEvenNumbers) {
                $this->assertLessThanOrEqual(
                    100,
                    array_sum($tripleOfEvenNumbers),
                    "The triple sum " . var_export($tripleOfEvenNumbers, true) . " is not less than 100"
                );
            });
    }

    // TODO: multiple generators means multiple values passed to map
}
