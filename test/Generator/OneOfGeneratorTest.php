<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class OneOfGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChooseGenerator
     */
    private $singleElementGenerator;
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp()
    {
        $this->singleElementGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testConstructWithAnArrayOfGenerators()
    {
        $generator = new OneOfGenerator([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $element = $generator($this->size, $this->rand);
        $this->assertInternalType('integer', $element->unbox());
    }

    public function testConstructWithNonGenerators()
    {
        $generator = new OneOfGenerator([42, 42]);
        $element = $generator($this->size, $this->rand)->unbox();
        $this->assertEquals(42, $element);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        $generator = new OneOfGenerator([]);
        $element = $generator($this->size, $this->rand);
    }
}
