<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class SuchThatGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testGeneratesAGeneratedValueObject(): void
    {
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n % 2 == 0;
            },
            ConstantGenerator::box(10)
        );
        $this->assertSame(
            10,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testAcceptsPHPUnitConstraints(): void
    {
        $generator = new SuchThatGenerator(
            $this->callback(function ($n) {
                return $n % 2 == 0;
            }),
            ConstantGenerator::box(10)
        );
        $this->assertSame(
            10,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOriginalInput(): void
    {
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n % 2 == 0;
            },
            new ChooseGenerator(0, 100)
        );
        $element = $generator->__invoke($this->size, $this->rand);
        for ($i = 0; $i < 100; $i++) {
            $element = $generator->shrink($element)->last();
            $this->assertSame(
                $element->unbox() % 2,
                0,
                "Element should still be filtered while shrinking: " . var_export($element, true)
            );
        }
    }

    public function testGivesUpGenerationIfTheFilterIsNotSatisfiedTooManyTimes(): void
    {
        $this->expectException(\Eris\Generator\SkipValueException::class);
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n % 2 == 0;
            },
            ConstantGenerator::box(11)
        );
        $generator->__invoke($this->size, $this->rand);
    }

    public function testGivesUpShrinkingIfTheFilterIsNotSatisfiedTooManyTimes(): void
    {
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n % 250 == 0;
            },
            new ChooseGenerator(0, 1000)
        );
        $unshrinkable = GeneratedValueSingle::fromJustValue(470);
        $this->assertEquals(
            $unshrinkable,
            $generator->shrink($unshrinkable)
        );
    }
    
    public function testShrinksMultipleOptionsButFiltersTheOnesThatSatisfyTheCondition(): void
    {
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n % 2 == 0;
            },
            new IntegerGenerator()
        );
        $element = GeneratedValueSingle::fromJustValue(100);
        $options = $generator->shrink($element);
        foreach ($options as $option) {
            $this->assertSame(
                $option->unbox() % 2,
                0,
                "Option should still be filtered while shrinking: " . var_export($option, true)
            );
        }
    }

    public function testThanksToMultipleShrinkingItCanBeLikelyToFindShrunkValuesWithRespectToOnlyFollowingThePessimistRoute(): void
    {
        $generator = new SuchThatGenerator(
            function ($n) {
                return $n < 250;
            },
            new IntegerGenerator()
        );
        $unshrinkable = GeneratedValueSingle::fromJustValue(470);
        $options = $generator->shrink($unshrinkable);
        $this->assertGreaterThan(0, count($options));
        foreach ($options as $option) {
            $this->assertLessThan(250, $option->unbox());
        }
    }
}
