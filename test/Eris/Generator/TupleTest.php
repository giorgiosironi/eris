<?php
namespace Eris\Generator;

class TupleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->generatorForSingleElement = new Natural(0, 100);
        $this->size = 10;
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $generated = $generator($this->size);

        $this->assertSame(2, count($generated));
        foreach ($generated as $element) {
            $this->assertTrue(
                $this->generatorForSingleElement->contains($element)
            );
        }
    }

    public function testConstructWithNonGenerators()
    {
        $aNonGenerator = 42;
        $generator = new Tuple([$aNonGenerator]);

        $generated = $generator($this->size);

        foreach ($generated as $element) {
            $this->assertTrue(
                (new Constant($aNonGenerator))->contains($element)
            );
        }
    }

    public function testConstructWithNoArguments()
    {
        $generator = new Tuple([]);

        $this->assertSame([], $generator($this->size));
    }

    public function testContainsGeneratedElements()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $tupleThatCanBeGenerated = [
            $this->generatorForSingleElement->__invoke($this->size),
            $this->generatorForSingleElement->__invoke($this->size),
        ];

        $this->assertTrue($generator->contains($tupleThatCanBeGenerated));
    }

    public function testShrink()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $elements = [42, 42];
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertTrue($this->generatorForSingleElement->contains($elementsAfterShrink[0]));
        $this->assertTrue($this->generatorForSingleElement->contains($elementsAfterShrink[1]));

        $this->assertLessThan(
            $elements[0] + $elements[1],
            $elementsAfterShrink[0] + $elementsAfterShrink[1]
        );
    }

    public function testShrinkSomethingAlreadyShrunkToTheMax()
    {
        $constants = [42, 42];
        $generator = new Tuple($constants);
        $elements = $generator($this->size);
        $this->assertSame($constants, $elements);
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame($constants, $elementsAfterShrink);
    }

    public function testShrinkNothing()
    {
        $generator = new Tuple([]);
        $elements = $generator($this->size);
        $this->assertSame([], $elements);
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertSame([], $elementsAfterShrink);
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);
        $generator->shrink([1.0, 25.6]);
    }
}
