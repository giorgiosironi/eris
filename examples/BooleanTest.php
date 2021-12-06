<?php

use Eris\Generators;
use Eris\TestTrait;

class BooleanTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testBooleanValueIsTrueOrFalse()
    {
        $this->forAll(
            Generators::bool()
        )
            ->then(function ($boolValue) {
                $this->assertTrue(
                    ($boolValue === true || $boolValue === false),
                    "$boolValue is not true nor false"
                );
            });
    }
}
