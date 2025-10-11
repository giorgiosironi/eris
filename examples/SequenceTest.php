<?php

use Eris\Generators;

class SequenceTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testArrayReversePreserveLength(): void
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array): void {
                self::assertCount(count($array), array_reverse($array));
            });
    }

    public function testArrayReverse(): void
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array): void {
                $this->assertEquals($array, array_reverse(array_reverse($array)));
            });
    }

    public function testArraySortingIsIdempotent(): void
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array): void {
                sort($array);
                $expected = $array;
                sort($array);
                $this->assertEquals($expected, $array);
            });
    }
}
