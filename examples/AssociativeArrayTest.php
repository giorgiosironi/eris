<?php

use Eris\Generators;

class AssociativeArrayTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testAssociativeArraysGeneratedOnStandardKeys(): void
    {
        $this->forAll(
            Generators::associative([
                'letter' => Generators::elements("A", "B", "C"),
                'cipher' => Generators::choose(0, 9),
            ])
        )
            ->then(function (array $array): void {
                $this->assertCount(2, $array);
                $letter = $array['letter'];
                self::assertIsString($letter);
                $cipher = $array['cipher'];
                self::assertIsInt($cipher);
            });
    }
}
