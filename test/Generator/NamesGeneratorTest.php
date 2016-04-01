<?php
namespace Eris\Generator;

class NamesGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testItRespectsTheGenerationSize()
    {
        $generator = NamesGenerator::defaultDataSet();
        for ($i = 5; $i < 50; $i++) {
            $value = $generator($maxLength = $i)->unbox();
            $this->assertTrue(
                $maxLength >= strlen($value),
                "Names generator is not respecting the generation size. Asked a name with max size {$maxLength} and returned {$value}"
            );
        }
    }

    public function testGeneratesANameFromAFixedDataset()
    {
        $generator = NamesGenerator::defaultDataSet();
        for ($i = 0; $i < 50; $i++) {
            $value = $generator($_size = 10);
            $this->assertTrue($generator->contains($value), "Generator does not contain the value `$value` which has generated");
        }
    }

    public static function namesToShrink()
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
    public function testShrinksToTheNameWithTheImmediatelyLowerLengthWhichHasTheMinimumDistance($shrunk, $original)
    {
        $generator = NamesGenerator::defaultDataSet();
        $this->assertEquals(
            $shrunk,
            $generator->shrink(GeneratedValue::fromJustValue($original))
                ->unbox()
        );
    }

    public function testContainsAllTheNamesInTheSpecifiedDataSet()
    {
        $generator = NamesGenerator::defaultDataSet();
        $this->assertTrue($generator->contains(GeneratedValue::fromJustValue("Bob")));
        $this->assertFalse($generator->contains(GeneratedValue::fromJustValue("Daitarn")));
    }
}
