<?php
namespace Eris\Generator;
use DateTime;

class DateGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
        $this->rand = 'rand';
    }

    public function testGenerateDateTimeObjectsInTheGivenInterval()
    {
        $generator = new DateGenerator(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $value = $generator($this->size, $this->rand);
        $this->assertTrue($generator->contains($value));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotShrinkValuesOutsideOfItsDomain()
    {
        $generator = new DateGenerator(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $value = $generator($this->size, $this->rand);
        $generator->shrink(GeneratedValue::fromJustValue(new DateTime("2014-01-10T00:00:00"), 'date'));
    }

    public function testDateTimeShrinkGeometrically()
    {
        $generator = new DateGenerator(
            new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $this->assertEquals(
            new DateTime("2014-01-01T16:00:00"),
            $generator->shrink(GeneratedValue::fromJustValue(
                new DateTime("2014-01-02T08:00:00"),
                'date'
            ))->unbox()
        );
    }

    public function testTheLowerLimitIsTheFixedPointOfShrinking()
    {
        $generator = new DateGenerator(
            $lowerLimit = new DateTime("2014-01-01T00:00:00"),
            new DateTime("2014-01-02T23:59:59")
        );
        $value = GeneratedValue::fromJustValue(
            new DateTime("2014-01-01T00:01:00"),
            'date'
        );
        for ($i = 0; $i < 10; $i++) {
            $value = $generator->shrink($value);
        }
        $this->assertEquals(
            $lowerLimit,
            $value->unbox()
        );
    }
}
