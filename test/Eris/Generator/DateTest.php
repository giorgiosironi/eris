<?php
namespace Eris\Generator;
use DateTime;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateDateTimeObjectsInTheGivenInterval()
    {
        $generator = new Date(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $value = $generator();
        $this->assertTrue($generator->contains($value));
    }

    public function testItIsStillUnclearHowCanDateTimeObjectShrink()
    {
        $generator = new Date(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $sampleValue = new DateTime("2014-01-01T12:00:00");
        $this->assertEquals($sampleValue, $generator->shrink($sampleValue));
    }
}
