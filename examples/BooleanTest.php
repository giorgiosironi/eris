<?php

use Eris\Generator\BooleanGenerator;
use Eris\TestTrait;

class BooleanTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testBooleanValueIsTrueOrFalse()
    {
        $this->forAll(
            BooleanGenerator::bool()
        )
            ->then(function ($boolValue) {
                $this->assertTrue(
                    ($boolValue === true || $boolValue === false),
                    "$boolValue is not true nor false"
                );
            });
    }
}
