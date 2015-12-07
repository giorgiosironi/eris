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
                    function($n) { return $n * 2; },
                    Generator\nat()
                )
            )
        )
            ->then(function($tripleOfEvenNumbers) {
                var_Dump($tripleOfEvenNumbers);
                foreach ($tripleOfEvenNumbers as $number) {
                    $this->assertTrue(
                        $number % 2 == 0,
                        "The element of the vector $number is not even"
                    );
                }
            });
    }
}
