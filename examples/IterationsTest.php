<?php
use Eris\Generator;
use Eris\TestTrait;

class IterationsTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testNumberOfIterationsCanBeConfigured()
    {
        $this->iterations = 5;
        $this->forAll(
            Generator\int()
        )
            ->then(function($value) {
                $this->assertInternalType('integer', $value);
            });
    }
}
