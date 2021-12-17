<?php

use Eris\Generators;

class ReadmeTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testNaturalNumbersMagnitude()
    {
        $this->forAll(
            Generators::choose(0, 1000)
        )
            ->then(function ($number) {
                $this->assertTrue(
                    $number < 42,
                    "$number is not less than 42 apparently"
                );
            });
    }
}
