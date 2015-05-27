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
    private $lowerLimit;
    private $upperLimit;

    public function __construct($oneLimit, $otherLimit)
    {
        $this->checkLimits($oneLimit, $otherLimit);

        $this->lowerLimit = min($oneLimit, $otherLimit);
        $this->upperLimit = max($oneLimit, $otherLimit);
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

    private function checkLimits($oneLimit, $otherLimit)
    {
        if ((!is_int($oneLimit)) || (!is_int($otherLimit))) {
            throw new InvalidArgumentException(
                'oneLimit (' . var_export($oneLimit, true) . ') and ' .
                'otherLimit (' . var_export($otherLimit, true) . ') should ' .
                'be Integers between 0 ' . ' and ' . PHP_INT_MAX
            );
        }

        if ($oneLimit < 0 || $otherLimit < 0) {
            throw new InvalidArgumentException('Natural generator lower limit must be >= 0');
        }
    }

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new DomainException(
                'Cannot shrink ' . $value . ' because it does not belong to the domain of ' .
                'Naturals between ' . $this->lowerLimit . ' and ' . $this->upperLimit
            );
        }
    }
}
