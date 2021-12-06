<?php

use Eris\Generators;
use Eris\Listeners;

class SuchThatTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testSuchThatBuildsANewGeneratorFilteringTheInnerOne()
    {
        $this
            ->forAll(
                Generators::vector(
                    5,
                    Generators::suchThat(
                        function ($n) {
                            return $n > 42;
                        },
                        Generators::choose(0, 1000)
                    )
                )
            )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function testFilterSyntax()
    {
        $this
            ->forAll(
                Generators::vector(
                    5,
                    Generators::filter(
                        function ($n) {
                            return $n > 42;
                        },
                        Generators::choose(0, 1000)
                    )
                )
            )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function testSuchThatAcceptsPHPUnitConstraints()
    {
        $this
            ->forAll(
                Generators::vector(
                    5,
                    Generators::suchThat(
                        $this->isType('int'),
                        Generators::oneOf(
                            Generators::choose(0, 1000),
                            Generators::string()
                        )
                    )
                )
            )
            ->hook(Listeners::log(sys_get_temp_dir().'/eris-such-that.log'))
            ->then($this->allNumbersAreBiggerThan(42));
    }


    public function testSuchThatShrinkingRespectsTheCondition()
    {
        $this
            ->forAll(
                Generators::suchThat(
                    function ($n) {
                        return $n > 42;
                    },
                    Generators::choose(0, 1000)
                )
            )
            ->then($this->numberIsBiggerThan(100));
    }

    public function testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet()
    {
        $this
            ->forAll(
                Generators::suchThat(
                    function ($n) {
                        return $n <> 42;
                    },
                    Generators::choose(0, 1000)
                )
            )
            ->then($this->numberIsBiggerThan(100));
    }

    public function testSuchThatAvoidingTheEmptyListDoesNotGetStuckOnASmallGeneratorSize()
    {
        $this
            ->forAll(
                Generators::suchThat(
                    function (array $ints) {
                        return count($ints) > 0;
                    },
                    Generators::seq(Generators::int())
                )
            )
            ->then(function (array $ints) use (&$i) {
                $this->assertGreaterThanOrEqual(1, count($ints));
            })
        ;
    }

    public function allNumbersAreBiggerThan($lowerLimit)
    {
        return function ($vector) use ($lowerLimit) {
            foreach ($vector as $number) {
                $this->assertTrue(
                    $number > $lowerLimit,
                    "\$number was asserted to be more than $lowerLimit, but it's $number"
                );
            }
        };
    }

    public function numberIsBiggerThan($lowerLimit)
    {
        return function ($number) use ($lowerLimit) {
            $this->assertTrue(
                $number > $lowerLimit,
                "\$number was asserted to be more than $lowerLimit, but it's $number"
            );
        };
    }
}
