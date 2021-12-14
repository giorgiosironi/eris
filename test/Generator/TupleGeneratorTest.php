<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class TupleGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ChooseGenerator
     */
    private $generatorForSingleElement;
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
        $this->generatorForSingleElement = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    private function assertInSingleElementGenerator($value): void
    {
        \Eris\PHPUnitDeprecationHelper::assertIsInt($value);
        $this->assertGreaterThanOrEqual(0, $value);
        $this->assertLessThanOrEqual(100, $value);
    }

    public function testConstructWithAnArrayOfGenerators(): void
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $generated = $generator($this->size, $this->rand);

        $this->assertCount(2, $generated->unbox());
        foreach ($generated->unbox() as $element) {
            $this->assertInSingleElementGenerator($element);
        }
    }

    public function testConstructWithNonGenerators(): void
    {
        $aNonGenerator = 42;
        $generator = new TupleGenerator([$aNonGenerator]);

        $generated = $generator($this->size, $this->rand);

        foreach ($generated->unbox() as $element) {
            $this->assertEquals(42, $element);
        }
    }

    public function testConstructWithNoArguments(): void
    {
        $generator = new TupleGenerator([]);

        $this->assertSame([], $generator($this->size, $this->rand)->unbox());
    }

    public function testShrink(): void
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $elements = $generator->__invoke($this->size, $this->rand);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertInSingleElementGenerator($elementsAfterShrink->unbox()[0]);
        $this->assertInSingleElementGenerator($elementsAfterShrink->unbox()[1]);

        $this->assertLessThanOrEqual(
            $elements->unbox()[0] + $elements->unbox()[1],
            $elementsAfterShrink->unbox()[0] + $elementsAfterShrink->unbox()[1],
            var_export(
                [
                    'elements' => $elements,
                    'elementsAfterShrink' => $elementsAfterShrink,
                ],
                true
            )
        );
    }

    public function testDoesNotShrinkSomethingAlreadyShrunkToTheMax(): void
    {
        $constants = [42, 42];
        $generator = new TupleGenerator($constants);
        $elements = $generator($this->size, $this->rand);
        $this->assertSame($constants, $elements->unbox());
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame($constants, $elementsAfterShrink->unbox());
    }

    public function testShrinkingMultipleOptionsOfOneGenerator(): void
    {
        $generator = new TupleGenerator([
            new IntegerGenerator()
        ]);
        $value = GeneratedValueSingle::fromValueAndInput(
            [100],
            [GeneratedValueSingle::fromJustValue(100, 'integer')],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        $this->assertGreaterThan(1, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            \Eris\PHPUnitDeprecationHelper::assertIsArray($optionValue);
            $this->assertCount(1, $optionValue);
        }
    }

    /**
     * @depends testShrinkingMultipleOptionsOfOneGenerator
     */
    public function testShrinkingMultipleOptionsOfMoreThanOneSingleShrinkingGenerator(): void
    {
        $generator = new TupleGenerator([
            new StringGenerator(),
            new StringGenerator(),
        ]);
        $value = GeneratedValueSingle::fromValueAndInput(
            ['hello', 'world'],
            [
                GeneratedValueSingle::fromJustValue('hello', 'string'),
                GeneratedValueSingle::fromJustValue('world', 'string'),
            ],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        // shrinking (a), (b) or (a and b)
        $this->assertEquals(3, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            \Eris\PHPUnitDeprecationHelper::assertIsArray($optionValue);
            $this->assertEquals(2, count($optionValue));
            $elementsBeingShrunk =
                (strlen($optionValue[0]) < 5 ? 1 : 0)
                + (strlen($optionValue[1]) < 5 ? 1 : 0);
            $this->assertGreaterThanOrEqual(1, $elementsBeingShrunk);
        }
    }

    /**
     * @depends testShrinkingMultipleOptionsOfOneGenerator
     */
    public function testShrinkingMultipleOptionsOfMoreThanOneMultipleShrinkingGenerator(): void
    {
        $generator = new TupleGenerator([
            new IntegerGenerator(),
            new IntegerGenerator(),
        ]);
        $value = GeneratedValueSingle::fromValueAndInput(
            [100, 200],
            [
                GeneratedValueSingle::fromJustValue(100, 'integer'),
                GeneratedValueSingle::fromJustValue(200, 'integer'),
            ],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        $this->assertGreaterThan(1, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            \Eris\PHPUnitDeprecationHelper::assertIsArray($optionValue);
            $this->assertCount(2, $optionValue);
            $this->assertNotEquals([100, 200], $optionValue);
            $elementsBeingShrunk =
                ($optionValue[0] < 100 ? 1 : 0)
                + ($optionValue[1] < 200 ? 1 : 0);
            $this->assertGreaterThanOrEqual(1, $elementsBeingShrunk);
        }
    }
}
