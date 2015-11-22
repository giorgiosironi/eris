<?php
namespace Eris\Generator;

use Eris\Generator;
use DateTime;
use DomainException;

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
    return new DateGenerator(
        $withDefault($box($lowerLimit), new DateTime("@0")),
        $withDefault($box($upperLimit), new DateTime("@" . getrandmax()))
    );
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

    public function __invoke($_size)
    {
        $generatedOffset = rand(0, $this->intervalInSeconds);
        return $this->fromOffset($generatedOffset);
    }

    public function shrink($element)
    {
        $this->ensureIsInDomain($element);

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

    private function ensureIsInDomain($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException("The element " . var_export($element, true) . " is not part of this generator's domain");
        }
    }
}
