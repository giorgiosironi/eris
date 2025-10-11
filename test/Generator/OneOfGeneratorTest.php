<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class OneOfGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private \Eris\Generator\ChooseGenerator $singleElementGenerator;
    private int $size;
    private \Eris\Random\RandomRange $rand;

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
        self::assertIsInt($element->unbox());
    }

    public function testConstructWithNonGenerators(): void
    {
        $generator = new OneOfGenerator([42, 42]);
        $element = $generator($this->size, $this->rand)->unbox();
        $this->assertEquals(42, $element);
    }

    public function testConstructWithNoArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $generator = new OneOfGenerator([]);
        $generator($this->size, $this->rand);
    }
}
