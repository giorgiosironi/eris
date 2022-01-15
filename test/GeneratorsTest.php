<?php

declare(strict_types=1);

namespace Eris;

use PHPUnit\Framework\TestCase;

class GeneratorsTest extends TestCase
{
    use TestTrait;

    public function test(): void
    {
        // This is not a real test
        // I just want to check Psalm won't complain about generators functions

        $this
            ->forAll(
                Generators::oneOf(Generators::pos(), Generators::neg())
            )
            ->then(function (int $x) {
                self::assertNotSame(0, $x);
            });
    }
}
