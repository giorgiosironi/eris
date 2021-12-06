<?php

use Eris\Generators;

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testGenericExceptionsDoNotShrinkButStillShowTheInput()
    {
        $this->forAll(
            Generators::string()
        )
            ->then(function ($string) {
                throw new RuntimeException("Something like a missing array index happened.");
            });
    }
}
