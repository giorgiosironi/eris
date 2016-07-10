<?php
namespace Eris\Generator;

class TupleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->generatorForSingleElement = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = 'rand';
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $generated = $generator($this->size, $this->rand);

        $this->assertSame(2, count($generated->unbox()));
        foreach ($generated->unbox() as $element) {
            $this->assertTrue(
                $this->generatorForSingleElement->contains(GeneratedValue::fromJustValue($element))
            );
        }
    }

    public function testConstructWithNonGenerators()
    {
        $aNonGenerator = 42;
        $generator = new TupleGenerator([$aNonGenerator]);

        $generated = $generator($this->size, $this->rand);

        foreach ($generated->unbox() as $element) {
            $this->assertTrue(
                (new ConstantGenerator($aNonGenerator))->contains(
                    GeneratedValue::fromJustValue($element)
                )
            );
        }
    }

    public function testConstructWithNoArguments()
    {
        $generator = new TupleGenerator([]);

        $this->assertSame([], $generator($this->size, $this->rand)->unbox());
    }

    public function testContainsGeneratedElements()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $tupleThatCanBeGenerated = GeneratedValue::fromJustValue([
            $this->generatorForSingleElement->__invoke($this->size, $this->rand),
            $this->generatorForSingleElement->__invoke($this->size, $this->rand),
        ]);

        $this->assertTrue($generator->contains($tupleThatCanBeGenerated));
    }

    public function testShrink()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $elements = $generator->__invoke($this->size, $this->rand);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertTrue($this->generatorForSingleElement->contains(
            GeneratedValue::fromJustValue($elementsAfterShrink->unbox()[0]))
        );
        $this->assertTrue($this->generatorForSingleElement->contains(
            GeneratedValue::fromJustValue($elementsAfterShrink->unbox()[1]))
        );

        $this->assertLessThan(
            $elements->unbox()[0] + $elements->unbox()[1],
            $elementsAfterShrink->unbox()[0] + $elementsAfterShrink->unbox()[1]
        );
    }

    public function testDoesNotShrinkSomethingAlreadyShrunkToTheMax()
    {
        $constants = [42, 42];
        $generator = new TupleGenerator($constants);
        $elements = $generator($this->size, $this->rand);
        $this->assertSame($constants, $elements->unbox());
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame($constants, $elementsAfterShrink->unbox());
    }

    public function testShrinkingMultipleOptionsOfOneGenerator()
    {
        $generator = new TupleGenerator([
            new IntegerGenerator()
        ]);
        $value = GeneratedValue::fromValueAndInput(
            [100],
            [GeneratedValue::fromJustValue(100, 'integer')],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        $this->assertGreaterThan(1, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            $this->assertInternalType('array', $optionValue);
            $this->assertEquals(1, count($optionValue));
        }
    }

    /**
     * @depends testShrinkingMultipleOptionsOfOneGenerator
     */
    public function testShrinkingMultipleOptionsOfMoreThanOneSingleShrinkingGenerator()
    {
        $generator = new TupleGenerator([
            new StringGenerator(),
            new StringGenerator(),
        ]);
        $value = GeneratedValue::fromValueAndInput(
            ['hello', 'world'],
            [
                GeneratedValue::fromJustValue('hello', 'string'),
                GeneratedValue::fromJustValue('world', 'string'),
            ],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        // shrinking (a), (b) or (a and b)
        $this->assertEquals(3, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            $this->assertInternalType('array', $optionValue);
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
    public function testShrinkingMultipleOptionsOfMoreThanOneMultipleShrinkingGenerator()
    {
        $generator = new TupleGenerator([
            new IntegerGenerator(),
            new IntegerGenerator(),
        ]);
        $value = GeneratedValue::fromValueAndInput(
            [100, 200],
            [
                GeneratedValue::fromJustValue(100, 'integer'),
                GeneratedValue::fromJustValue(200, 'integer'),
            ],
            'tuple'
        );
        $shrunk = $generator->shrink($value);
        $this->assertGreaterThan(1, $shrunk->count());
        foreach ($shrunk as $option) {
            $this->assertEquals('tuple', $option->generatorName());
            $optionValue = $option->unbox();
            $this->assertInternalType('array', $optionValue);
            $this->assertEquals(2, count($optionValue));
            // TODO: put in OR, as [99, 200] and [100, 199] should be good
            $elementsBeingShrunk =
                ($optionValue[0] < 100 ? 1 : 0)
                + ($optionValue[1] < 200 ? 1 : 0);
            $this->assertGreaterThanOrEqual(1, $elementsBeingShrunk);
        }
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);
        $generator->shrink(GeneratedValue::fromJustValue([1, 2, 3]));
    }
}
