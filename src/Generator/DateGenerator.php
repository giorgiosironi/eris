<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;
use DateTime;

/**
 * @param DateTime|null $lowerLimit
 * @param DateTime|null $upperLimit
 * @return DateGenerator
 * @throws \Exception
 */
function date($lowerLimit = null, $upperLimit = null)
{
    return DateGenerator::date($lowerLimit, $upperLimit);
}

class DateGenerator implements Generator
{
    private $lowerLimit;
    private $upperLimit;
    private $intervalInSeconds;

    public function __construct(DateTime $lowerLimit, DateTime $upperLimit)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
        $this->intervalInSeconds = $upperLimit->getTimestamp() - $lowerLimit->getTimestamp();
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $generatedOffset = $rand->rand(0, $this->intervalInSeconds);
        return GeneratedValueSingle::fromJustValue(
            $this->fromOffset($generatedOffset),
            'date'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        $timeOffset = $element->unbox()->getTimestamp() - $this->lowerLimit->getTimestamp();
        $halvedOffset = floor($timeOffset / 2);
        return GeneratedValueSingle::fromJustValue(
            $this->fromOffset($halvedOffset),
            'date'
        );
    }

    /**
     * @param integer $offset  seconds to be added to lower limit
     * @return DateTime
     */
    private function fromOffset($offset)
    {
        $chosenTimestamp = $this->lowerLimit->getTimestamp() + $offset;
        $element = new DateTime();
        $element->setTimestamp($chosenTimestamp);
        return $element;
    }

    /**
     * @param DateTime|null $lowerLimit
     * @param DateTime|null $upperLimit
     * @return DateGenerator
     * @throws \Exception
     */
    public static function date($lowerLimit = null, $upperLimit = null)
    {
        $box = static function ($date) {
            if ($date === null) {
                return $date;
            }
            if ($date instanceof DateTime) {
                return $date;
            }
            return new DateTime($date);
        };
        $withDefault = static function ($value, $default) {
            if ($value !== null) {
                return $value;
            }
            return $default;
        };
        return new self(
            $withDefault($box($lowerLimit), new DateTime("@0")),
            // uses a maximum which is conservative
            $withDefault($box($upperLimit), new DateTime("@" . (pow(2, 31) - 1)))
        );
    }
}
