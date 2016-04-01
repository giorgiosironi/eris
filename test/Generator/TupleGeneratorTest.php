<?php
namespace Eris\Generator;

class TupleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->generatorForSingleElement = new ChooseGenerator(0, 100);
        $this->size = 10;
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $generated = $generator($this->size);

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

        $generated = $generator($this->size);

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

        $this->assertSame([], $generator($this->size)->unbox());
    }

    public function testContainsGeneratedElements()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $tupleThatCanBeGenerated = GeneratedValue::fromJustValue([
            $this->generatorForSingleElement->__invoke($this->size),
            $this->generatorForSingleElement->__invoke($this->size),
        ]);

        $this->assertTrue($generator->contains($tupleThatCanBeGenerated));
    }

    public function testShrink()
    {
        $generator = new TupleGenerator([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $elements = $generator->__invoke($this->size);
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
        $elements = $generator($this->size);
        $this->assertSame($constants, $elements->unbox());
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame($constants, $elementsAfterShrink->unbox());
    }

    public function testShrinkNothing()
    {
        $generator = new TupleGenerator([]);
        $elements = $generator($this->size);
        $this->assertSame([], $elements->unbox());
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame([], $elementsAfterShrink->unbox());
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
