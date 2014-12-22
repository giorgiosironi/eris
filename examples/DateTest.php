<?php
use Eris\Generator;

class DateTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testYearOfADate()
    {
        $this->forAll([
            Generator\date("2014-01-01T00:00:00", "2014-12-31T23:59:59"),
        ])
            ->then(function(DateTime $date) {
                $this->assertEquals(
                    "2014",
                    $date->format('Y')
                );
            });
    }

    public function testDefaultValuesForTheInterval()
    {
        $this->forAll([
            Generator\date(),
        ])
            ->then(function(DateTime $date) {
                $this->assertGreaterThanOrEqual(
                    "1970",
                    $date->format('Y')
                );
                $this->assertLessThanOrEqual(
                    "2038",
                    $date->format('Y')
                );
            });
    }
}
