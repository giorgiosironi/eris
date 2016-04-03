<?php
use Eris\Generator;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testGenericExceptionsDoNotShrinkButStillShowTheInput()
    {
        $this->forAll(
            Generator\string()
        )
            ->then(function ($string) {
                throw new RuntimeException("Something like a missing array index happened.");
            });
    }
}
