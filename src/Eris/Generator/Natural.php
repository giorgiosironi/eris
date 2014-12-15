<?php
namespace Eris\Generator;
use Eris\Generator;
use InvalidArgumentException;
use DomainException;

function nat($upperLimit = PHP_INT_MAX)
{
    return new Natural(0, $upperLimit);
}

class Natural implements Generator
{
    public function __construct($lowerLimit, $upperLimit)
    {
        $this->checkLimits($lowerLimit, $upperLimit);

        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
    }

    public function __invoke()
    {
        return rand($this->lowerLimit, $this->upperLimit);
    }

    public function shrink($element)
    {
        $this->checkValueToShrink($element);

        if ($element > $this->lowerLimit) {
            $element--;
        }

        return $element;
    }

    public function contains($element)
    {
        return is_numeric($element)
            && ($element === (int) floor($element))
            && ($element === (int) ceil($element))
            && ($element >= $this->lowerLimit)
            && ($element <= $this->upperLimit);
    }

    private function checkLimits($lowerLimit, $upperLimit)
    {
        if ((!is_int($lowerLimit)) || (!is_int($upperLimit))) {
            throw new InvalidArgumentException(
                "lowerLimit (" . var_export($lowerLimit, true) . ") and " .
                "upperLimit (" . var_export($upperLimit, true) . ") should " .
                "be Integers between 0 " . " and " . PHP_INT_MAX
            );
        }

        if ($lowerLimit < 0) {
            throw new InvalidArgumentException('Natural generator lower limit must be >= 0');
        }

        if ($lowerLimit > $upperLimit) {
            throw new InvalidArgumentException(
                "lower limit must be lower than the upper limit. " .
                "in this case {$lowerLimit} is not lower than {$upperLimit}."
            );
        }
    }

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new DomainException(
                "Cannot shrink {$value} because does not belongs to the domain of " .
                "Naturals between {$this->lowerLimit} and {$this->upperLimit}"
            );
        }
    }
}
