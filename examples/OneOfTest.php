<?php

use Eris\Generators;

class OneOfTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testPositiveOrNegativeNumberButNotZero()
    {
        $this
            ->forAll(
                Generators::oneOf(
                    Generators::pos(),
                    Generators::neg()
                )
            )
            ->then(function ($number) {
                $this->assertNotEquals(0, $number);
            });
    }
}
