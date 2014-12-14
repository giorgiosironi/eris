<?php
namespace Eris\Generator;

class NamesTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesANameFromAFixedDataset()
    {
        $generator = Names::defaultDataSet();
        var_dump($generator());
    }

    public function testShrinksToTheNameWithTheImmediatelyLowerLengthWhichHasTheMinimumDistance()
    {
        $generator = Names::defaultDataSet();
        var_Dump($generator->shrink("Maxence"));
    }
}
