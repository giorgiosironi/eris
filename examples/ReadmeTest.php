<?php
class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testNaturalNumbersMagnitude()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->then(function($number) {
                $this->assertTrue(
                    $number < 42,
                    "$number is not less than 42 apparently"
                );
            });
    }
}
