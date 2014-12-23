<?php
use Eris\Generator;

class ErrorMessagesTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testTheInputIsShownEvenIfAGenericExceptionHappens()
    {
        $this
            ->forAll([
                Generator\int(0, 100)
            ])
            ->then(function($number) {
                my_sqrt_function($number);
            });
    }
}

function my_sqrt_function($input)
{
    throw new RuntimeException("Something bad happened");
}
