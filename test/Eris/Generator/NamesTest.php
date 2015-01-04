<?php
namespace Eris\Generator;

class NamesTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesANameFromAFixedDataset()
    {
        $generator = Names::defaultDataSet();
        for ($i = 0; $i < 50; $i++) {
            $value = $generator();
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
    }
}
