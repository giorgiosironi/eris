<?php

use Eris\Generators;

class MapTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testApplyingAFunctionToGeneratedValues()
    {
        $this->forAll(
            Generators::vector(
                3,
                Generators::map(
                    function ($n) {
                        return $n * 2;
                    },
                    Generators::nat()
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
            Generators::map(
                function ($n) {
                    return $n * 2;
                },
                Generators::nat()
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
            Generators::vector(
                3,
                Generators::map(
                    function ($n) {
                        return $n * 2;
                    },
                    Generators::nat()
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
