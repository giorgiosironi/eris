<?php
use Eris\Generator\ChooseGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\OneOfGenerator;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\StringGenerator;
use Eris\Generator\SuchThatGenerator;
use Eris\Generator\VectorGenerator;
use Eris\Listener;

class SuchThatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSuchThatBuildsANewGeneratorFilteringTheInnerOne()
    {
        $this
            ->forAll(
                VectorGenerator::vector(
                    5,
                    SuchThatGenerator::suchThat(
                        function ($n) {
                            return $n > 42;
                        },
                        ChooseGenerator::choose(0, 1000)
                    )
                )
            )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function testFilterSyntax()
    {
        $this
            ->forAll(
                VectorGenerator::vector(
                    5,
                    SuchThatGenerator::filter(
                        function ($n) {
                            return $n > 42;
                        },
                        ChooseGenerator::choose(0, 1000)
                    )
                )
            )
            ->then($this->allNumbersAreBiggerThan(42));
    }

    public function testSuchThatAcceptsPHPUnitConstraints()
    {
        $this
            ->forAll(
                VectorGenerator::vector(
                    5,
                    SuchThatGenerator::suchThat(
                        $this->isType('integer'),
                        OneOfGenerator::oneOf(
                            ChooseGenerator::choose(0, 1000),
                            StringGenerator::string()
                        )
                    )
                )
            )
            ->hook(Listener\log(sys_get_temp_dir().'/eris-such-that.log'))
            ->then($this->allNumbersAreBiggerThan(42));
    }


    public function testSuchThatShrinkingRespectsTheCondition()
    {
        $this
            ->forAll(
                SuchThatGenerator::suchThat(
                    function ($n) {
                        return $n > 42;
                    },
                    ChooseGenerator::choose(0, 1000)
                )
            )
            ->then($this->numberIsBiggerThan(100));
    }

    public function testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet()
    {
        $this
            ->forAll(
                SuchThatGenerator::suchThat(
                    function ($n) {
                        return $n <> 42;
                    },
                    ChooseGenerator::choose(0, 1000)
                )
            )
            ->then($this->numberIsBiggerThan(100));
    }

    public function testSuchThatAvoidingTheEmptyListDoesNotGetStuckOnASmallGeneratorSize()
    {
        $this
            ->forAll(
                SuchThatGenerator::suchThat(
                    function (array $ints) {
                        return count($ints) > 0;
                    },
                    SequenceGenerator::seq(IntegerGenerator::int())
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
