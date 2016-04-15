<?php
use Eris\Generator;

class OneOfTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testPositiveOrNegativeNumberButNotZero()
    {
        $this
            ->forAll(
                Generator\oneOf(
                    Generator\pos(),
                    Generator\neg()
                )
            )
            ->then(function ($number) {
                $this->assertNotEquals(0, $number);
            });
    }
}
