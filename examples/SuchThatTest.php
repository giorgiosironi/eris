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
            ->then(function($vector) {
                foreach ($vector as $number) {
                    $this->assertTrue(
                        $number > 42,
                        "\$number was filtered to be more than 42, but it's $number"
                    );
                }
            });
    }
}
