<?php
namespace Eris\Generator;

class TupleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->generatorForSingleElement = new Natural(0, 100);
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $generated = $generator();

        $this->assertEquals(2, count($generated));
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

        $generated = $generator();

        foreach ($generated as $element) {
            $this->assertTrue(
                (new Constant($aNonGenerator))->contains($element)
            );
        }
    }

    public function testConstructWithNoArguments()
    {
        $generator = new Tuple([]);

        $this->assertEquals([], $generator());
    }

    public function testContainsGeneratedElements()
    {
        $generator = new Tuple([
            $this->generatorForSingleElement,
            $this->generatorForSingleElement,
        ]);

        $tupleThatCanBeGenerated = [
            $this->generatorForSingleElement->__invoke(),
            $this->generatorForSingleElement->__invoke(),
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
        $elements = $generator();
        $this->assertEquals($constants, $elements);
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertEquals($constants, $elementsAfterShrink);
    }

    public function testShrinkNothing()
    {
        $generator = new Tuple([]);
        $elements = $generator();
        $this->assertEquals([], $elements);
        $elementsAfterShrink = $generator->shrink($elements);
        $this->assertEquals([], $elementsAfterShrink);
    }
}
