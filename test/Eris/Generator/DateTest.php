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

    public function testDateTimeShrinkGeometrically()
    {
        $generator = new Date(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $this->assertEquals(
            new DateTime("2014-01-01T16:00:00"),
            $generator->shrink(new DateTime("2014-01-02T08:00:00"))
        );
    }
}
