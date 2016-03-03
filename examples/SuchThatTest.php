<?php
use Eris\Generator;

class SuchThatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSuchThatBuildsANewGeneratorFilteringTheInnerOne()
    {
        $this->forAll(
            Generator\vector(
                5,
                Generator\suchThat(
                    function($n) {
                        return $n > 42;
                    },
                    Generator\choose(0, 1000)
                )
            )
        )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function testFilterSyntax()
    {
        $this->forAll(
            Generator\vector(
                5,
                Generator\filter(
                    function($n) {
                        return $n > 42;
                    },
                    Generator\choose(0, 1000)
                )
            )
        )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function allNumbersAreBiggerThan($lowerLimit)
    {
        return function($vector) use ($lowerLimit) {
            foreach ($vector as $number) {
                $this->assertTrue(
                    $number > $lowerLimit,
                    "\$number was filtered to be more than $lowerLimit, but it's $number"
                );
            }
        };
    }
}
