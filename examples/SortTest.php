<?php

use Eris\Generators;

class SortTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testArraySorting(): void
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array): void {
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
