<?php
namespace Eris\Generator;
use Eris\Generator;
use DateTime;

function date($lowerLimit, $upperLimit)
{
    $box = function($date) {
        if ($date instanceof DateTime) {
            return $date;
        }
        return new DateTime($date);
    };
    return new Date($box($lowerLimit), $box($upperLimit));
}

class Date implements Generator
{
    private $lowerLimit;
    private $upperLimit;
    
    public function __construct(DateTime $lowerLimit, DateTime $upperLimit)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
        $this->intervalInSeconds = $upperLimit->getTimestamp() - $lowerLimit->getTimestamp();
    }

    public function __invoke()
    {
        $timeOffset = rand(0, $this->intervalInSeconds);
        $chosenTimestamp = $this->lowerLimit->getTimestamp() + $timeOffset;
        $element = new DateTime();
        $element->setTimestamp($chosenTimestamp);
        return $element;
    }

    public function shrink($element)
    {
        return $element;
    }

    public function contains($element)
    {
        return $element instanceof DateTime
            && $element >= $this->lowerLimit
            && $element <= $this->upperLimit;
    }
}
