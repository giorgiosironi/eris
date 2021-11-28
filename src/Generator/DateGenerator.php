<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;
use DateTime;

function date($lowerLimit = null, $upperLimit = null)
{
    return Generators::date($lowerLimit, $upperLimit);
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
}
