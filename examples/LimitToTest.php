<?php
use Eris\Generator;
use Eris\TestTrait;

class LimitToTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testNumberOfIterationsCanBeConfigured()
    {
        $this->limitTo(5)
             ->forAll(
                Generator\int()
            )
            ->then(function($value) {
                $this->assertInternalType('integer', $value);
            });
    }
}
