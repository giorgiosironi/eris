<?php
namespace Eris\Generator;

class OneOfTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->singleElementGenerator = new Natural(0, 100);
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new OneOf([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $element = $generator();

        $this->assertTrue($this->singleElementGenerator->contains($element));
    }

    public function testConstructWithNonGenerators()
    {
        $generator = new OneOf([42, 42]);
        $element = $generator();
        $this->assertEquals(42, $element);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        $generator = new OneOf([]);
        $element = $generator();
    }

    public function testShrinkElementGeneratedFromLastGenerator()
    {
        $generator = new OneOf([21, 42]);
        $shrinked = $generator->shrink(42);
        $this->assertTrue($shrinked === 21 || $shrinked === 42);
    }

    public function testShrinkElementGenerateFromFirstGenerator()
    {
        $generator = new OneOf([21, 42]);
        $shrinked = $generator->shrink(21);
        $this->assertTrue($shrinked === 21);
    }

    public function testShrinkEventuallyEndsUpToTheSmallestElementOfTheFirstDomain()
    {
        $generator = new OneOf([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $numberOfShrinks = 0;
        $shrinked = 10;
        while ($shrinked !== 1) {
            $shrinked = $generator->shrink($shrinked);
            $numberOfShrinks += 1;
            $this->assertTrue($numberOfShrinks < 100000, 'Too many shrinks');
        }
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkElementsNotInDomain()
    {
        $elementNotInDomain = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($elementNotInDomain));

        $generator = new OneOf([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $generator->shrink($elementNotInDomain);
    }
}
