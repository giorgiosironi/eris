<?php
use Eris\Generator;

class DateTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testYearOfADate()
    {
        $this->forAll(
            Generator\date("2014-01-01T00:00:00", "2014-12-31T23:59:59")
        )
            ->then(function (DateTime $date) {
                $this->assertEquals(
                    "2014",
                    $date->format('Y')
                );
            });
    }

    public function testDefaultValuesForTheInterval()
    {
        $this->forAll(
            Generator\date()
        )
            ->then(function (DateTime $date) {
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

    public function testFromDayOfYearFactoryMethodRespectsDistanceBetweenDays()
    {
        $this->forAll(
            Generator\choose(2000, 2020),
            Generator\choose(0, 364),
            Generator\choose(0, 364)
        )
        ->then(function ($year, $dayOfYear, $anotherDayOfYear) {
            $day = fromZeroBasedDayOfYear($year, $dayOfYear);
            $anotherDay = fromZeroBasedDayOfYear($year, $anotherDayOfYear);
            $this->assertEquals(
                abs($dayOfYear - $anotherDayOfYear) * 86400,
                abs($day->getTimestamp() - $anotherDay->getTimestamp()),
                "Days of the year $year: $dayOfYear, $anotherDayOfYear" . PHP_EOL
                . "{$day->format(DateTime::ISO8601)}, {$anotherDay->format(DateTime::ISO8601)}"
            );
        });
    }
}

function fromZeroBasedDayOfYear($year, $dayOfYear)
{
    return DateTime::createFromFormat(
        'z Y H i s',
        $dayOfYear . ' '. $year . ' 00 00 00',
        new DateTimeZone("UTC")
    );
}
