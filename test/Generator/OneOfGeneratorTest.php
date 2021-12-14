<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class OneOfGeneratorTest extends \PHPUnit\Framework\TestCase
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

    protected function setUp(): void
    {
        $this->singleElementGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testConstructWithAnArrayOfGenerators(): void
    {
        $generator = new OneOfGenerator([
            $this->singleElementGenerator,
            $this->singleElementGenerator,
        ]);

        $element = $generator($this->size, $this->rand);
        \Eris\PHPUnitDeprecationHelper::assertIsInt($element->unbox());
    }

    public function testConstructWithNonGenerators(): void
    {
        $generator = new OneOfGenerator([42, 42]);
        $element = $generator($this->size, $this->rand)->unbox();
        $this->assertEquals(42, $element);
    }

    public function testConstructWithNoArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $generator = new OneOfGenerator([]);
        $element = $generator($this->size, $this->rand);
    }
}
