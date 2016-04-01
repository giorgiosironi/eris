<?php
namespace Eris\Generator;

class OneOfGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->singleElementGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new OneOfGenerator([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $element = $generator($this->size);

        $this->assertTrue($generator->contains($element));
    }

    public function testConstructWithNonGenerators()
    {
        $generator = new OneOfGenerator([42, 42]);
        $element = $generator($this->size)->unbox();
        $this->assertEquals(42, $element);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        $generator = new OneOfGenerator([]);
        $element = $generator($this->size);
    }
}
