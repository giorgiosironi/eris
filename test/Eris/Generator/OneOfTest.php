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
        $generator = oneOf([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $element = $generator();

        $this->assertTrue($this->singleElementGenerator->contains($element));
    }

    public function testConstructWithNonGenerators()
    {
        $generator = oneOf([42, 42]);
        $element = $generator();
        $this->assertEquals(42, $element);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        $generator = oneOf([]);
        $element = $generator();
    }
}
