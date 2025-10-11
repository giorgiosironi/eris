<?php

use Eris\Generators;

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testGenericExceptionsDoNotShrinkButStillShowTheInput(): void
    {
        $this->forAll(
            Generators::string()
        )
            ->then(function ($string): void {
                throw new RuntimeException("Something like a missing array index happened.");
            });
    }
}
