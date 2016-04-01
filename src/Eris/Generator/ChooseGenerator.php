<?php
namespace Eris\Generator;

use Eris\Generator;
use InvalidArgumentException;
use DomainException;

if (!defined('ERIS_PHP_INT_MIN')) {
    define('ERIS_PHP_INT_MIN', ~PHP_INT_MAX);
}

class ChooseGenerator implements Generator
{
    private $lowerLimit;
    private $upperLimit;
    private $shrinkTarget;

    public function __construct($x, $y)
    {
        $this->checkLimits($x, $y);

        $this->lowerLimit = min($x, $y);
        $this->upperLimit = max($x, $y);
        $this->shrinkTarget = min(
            abs($this->lowerLimit),
            abs($this->upperLimit)
        );
    }

    public function __invoke($_size)
    {
        $value = rand($this->lowerLimit, $this->upperLimit);

        return GeneratedValue::fromJustValue($value, 'choose');
    }

    public function shrink(GeneratedValue $element)
    {
        $this->checkValueToShrink($element);

        if ($element->input() > $this->shrinkTarget) {
            return GeneratedValue::fromJustValue($element->input() - 1);
        }
        if ($element->input() < $this->shrinkTarget) {
            return GeneratedValue::fromJustValue($element->input() + 1);
        }

        return $element;
    }

    public function contains(GeneratedValue $element)
    {
        return is_int($element->input())
            && $element->input() >= $this->lowerLimit
            && $element->input() <= $this->upperLimit;
    }

    private function checkLimits($lowerLimit, $upperLimit)
    {
        // TODO: the problem with the random number generator is still here.
        if ((!is_int($lowerLimit)) || (!is_int($upperLimit))) {
            throw new InvalidArgumentException(
                'lowerLimit (' . var_export($lowerLimit, true) . ') and ' .
                'upperLimit (' . var_export($upperLimit, true) . ') should ' .
                'be Integers between ' . ERIS_PHP_INT_MIN . ' and ' . PHP_INT_MAX
            );
        }
    }

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new DomainException(
                'Cannot shrink ' . $value . ' because it does not belong to the domain of ' .
                'Integers between ' . $this->lowerLimit . ' and ' . $this->upperLimit
            );
        }
    }
}
