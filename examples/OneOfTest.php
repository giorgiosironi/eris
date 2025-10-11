<?php

use Eris\Generators;

class OneOfTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testPositiveOrNegativeNumberButNotZero(): void
    {
        $this
            ->forAll(
                Generators::oneOf(
                    Generators::pos(),
                    Generators::neg()
                )
            )
            ->then(function ($number): void {
                $this->assertNotEquals(0, $number);
            });
    }
}
