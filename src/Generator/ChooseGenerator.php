<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use InvalidArgumentException;
use Eris\Random\RandomRange;

if (!defined('ERIS_PHP_INT_MIN')) {
    define('ERIS_PHP_INT_MIN', ~PHP_INT_MAX);
}

/**
 * Generates a number in the range from the lower bound to the upper bound,
 * inclusive. The result shrinks towards smaller absolute values.
 * The order of the parameters does not care since they are re-ordered by the
 * generator itself.
 *
 * @param $x int One of the 2 boundaries of the range
 * @param $y int The other boundary of the range
 * @return Generator\ChooseGenerator
 */
function choose($lowerLimit, $upperLimit)
{
    return Generators::choose($lowerLimit, $upperLimit);
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

    public function __invoke($_size, RandomRange $rand)
    {
        $value = $rand->rand($this->lowerLimit, $this->upperLimit);

        return GeneratedValueSingle::fromJustValue($value, 'choose');
    }

    public function shrink(GeneratedValue $element)
    {
        if ($element->input() > $this->shrinkTarget) {
            return GeneratedValueSingle::fromJustValue($element->input() - 1);
        }
        if ($element->input() < $this->shrinkTarget) {
            return GeneratedValueSingle::fromJustValue($element->input() + 1);
        }

        return $element;
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
}
