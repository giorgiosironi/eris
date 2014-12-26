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

    public function testShrinkDisjointDomains()
    {
        $generator = new OneOf([42, 21]);
        $this->assertEquals(42, $generator->shrink(42));
        $this->assertEquals(21, $generator->shrink(21));
    }

    public function testShrinkIntersectingDomains()
    {
        $generator = new OneOf([
            new Natural(1, 100),
            new Natural(10, 100),
        ]);

        $element = 42;
        for ($i=0; $i<100; $i++) {
            $element = $generator->shrink($element);
        }

        $this->assertEquals(1, $element);
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkSomethingThatIsNotInDomain()
    {
        $generator = new OneOf([42, 21]);
        $generator->shrink('something');
    }
}
