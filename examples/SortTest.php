<?php
use Eris\Generator;

class SortTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testArraySorting()
    {
        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function ($array) {
                sort($array);
                for ($i = 0; $i < count($array) - 1; $i++) {
                    $this->assertTrue(
                        $array[$i] <= $array[$i+1],
                        "Array is not sorted: " . var_export($array, true)
                    );
                }
            });
    }
}
