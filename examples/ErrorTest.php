<?php


use Eris\Generator\StringGenerator;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testGenericExceptionsDoNotShrinkButStillShowTheInput()
    {
        $this->forAll(
            StringGenerator::string()
        )
            ->then(function ($string) {
                throw new RuntimeException("Something like a missing array index happened.");
            });
    }
}
