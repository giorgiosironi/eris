<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class NamesGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RandomRange
     */
    private $rand;

    public function setUp(): void
    {
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testItRespectsTheGenerationSize(): void
    {
        $generator = NamesGenerator::defaultDataSet();
        for ($i = 5; $i < 50; $i++) {
            $value = $generator($maxLength = $i, $this->rand)->unbox();
            $this->assertTrue(
                $maxLength >= strlen($value),
                "Names generator is not respecting the generation size. Asked a name with max size {$maxLength} and returned {$value}"
            );
        }
    }

    public function testGeneratesANameFromAFixedDataset(): void
    {
        $generator = NamesGenerator::defaultDataSet();
        for ($i = 0; $i < 50; $i++) {
            $value = $generator($_size = 10, $this->rand);
            \Eris\PHPUnitDeprecationHelper::assertIsString($value->unbox());
        }
    }

    public static function namesToShrink(): array
    {
        return [
            ["Malene", "Maxence"],
            ["Columban", "Columbano"],
            ["Carol-Anne", "Carole-Anne"],
            ["Annie", "Zinnia"],
            ["Aletta", "Lucetta"],
            ["Tekla", "Thekla"],
            ["Ursin", "Ursine"],
            ["Gwennan", "Gwenegan"],
            ["Eliane", "Eliabel"],
            ["Ed", "Ed"],
            ["Di", "Di"],
        ];
    }

    /**
     * @dataProvider namesToShrink
     */
    public function testShrinksToTheNameWithTheImmediatelyLowerLengthWhichHasTheMinimumDistance($shrunk, $original): void
    {
        $generator = NamesGenerator::defaultDataSet();
        $this->assertEquals(
            $shrunk,
            $generator->shrink(GeneratedValueSingle::fromJustValue($original))
                ->unbox()
        );
    }
}
