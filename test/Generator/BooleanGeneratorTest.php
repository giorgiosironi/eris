<?php
namespace Eris\Generator;

use Eris\PHPUnitDeprecationHelper;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class BooleanGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RandomRange
     */
    private $rand;

    public function setUp(): void
    {
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testRandomlyPicksTrueOrFalse(): void
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 0, $this->rand);
            PHPUnitDeprecationHelper::assertIsBool($generatedValue->unbox());
        }
    }

    public function testShrinksToFalse(): void
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 10, $this->rand);
            $this->assertFalse($generator->shrink($generatedValue)->unbox());
        }
    }
}
