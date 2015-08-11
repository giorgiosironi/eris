<?php
namespace Eris\Generator;

class NamesTest extends \PHPUnit_Framework_TestCase
{
    public function testItRespectsTheGenerationSize()
    {
        $generator = Names::defaultDataSet();
        for ($i = 5; $i < 50; $i++) {
            $value = $generator($maxLength = $i);
            $this->assertTrue(
                $maxLength >= strlen($value),
                "Names generator is not respecting the generation size. Asked a name with max size {$maxLength} and returned {$value}"
            );
        }
    }

    public function testGeneratesANameFromAFixedDataset()
    {
        $generator = Names::defaultDataSet();
        for ($i = 0; $i < 50; $i++) {
            $value = $generator($_size = 10);
            $this->assertTrue($generator->contains($value), "Generator does not contain the value `$value` which has generated");
        }
    }

    public function testShrinksToTheNameWithTheImmediatelyLowerLengthWhichHasTheMinimumDistance()
    {
        $generator = Names::defaultDataSet();
        $this->assertEquals("Malene", $generator->shrink("Maxence"));
        $this->assertEquals("Columban", $generator->shrink("Columbano"));
        $this->assertEquals("Carol-Anne", $generator->shrink("Carole-Anne"));
        $this->assertEquals("Annie", $generator->shrink("Zinnia"));
        $this->assertEquals("Aletta", $generator->shrink("Lucetta"));
        $this->assertEquals("Tekla", $generator->shrink("Thekla"));
        $this->assertEquals("Ursin", $generator->shrink("Ursine"));
        $this->assertEquals("Gwennan", $generator->shrink("Gwenegan"));
        $this->assertEquals("Eliane", $generator->shrink("Eliabel"));
        $this->assertEquals("Ed", $generator->shrink("Ed"));
        $this->assertEquals("Di", $generator->shrink("Di"));
    }

    public function testContainsAllTheNamesInTheSpecifiedDataSet()
    {
        $generator = Names::defaultDataSet();
        $this->assertTrue($generator->contains("Bob"));
        $this->assertFalse($generator->contains("Daitarn"));
    }
}
