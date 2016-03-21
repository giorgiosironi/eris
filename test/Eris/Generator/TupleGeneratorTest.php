<?php
namespace Eris\Generator;

class TupleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
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

    public function testShrinkNothing()
    {
        $generator = new TupleGenerator([]);
        $elements = $generator($this->size, $this->rand);
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
