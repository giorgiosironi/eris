<?php
namespace Eris\Generator;
use Eris\Generator;
use DateTime;

function date($lowerLimit = null, $upperLimit = null)
{
    $box = function($date) {
        if ($date === null) {
            return $date;
        }
        if ($date instanceof DateTime) {
            return $date;
        }
        return new DateTime($date);
    };
    $withDefault = function($value, $default) {
        if ($value !== null) {
            return $value;
        }
        return $default;
    };
    return new Date(
        $withDefault($box($lowerLimit), new DateTime("@0")),
        $withDefault($box($upperLimit), new DateTime("@" . getrandmax()))
    );
}

class Date implements Generator
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

    public function __invoke()
    {
        $generatedOffset = rand(0, $this->intervalInSeconds);
        return $this->fromOffset($generatedOffset);
    }

    public function shrink($element)
    {
        $timeOffset = $element->getTimestamp() - $this->lowerLimit->getTimestamp();
        $halvedOffset = floor($timeOffset / 2);
        return $this->fromOffset($halvedOffset);
    }

    public function contains($element)
    {
        return $element instanceof DateTime
            && $element >= $this->lowerLimit
            && $element <= $this->upperLimit;
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
