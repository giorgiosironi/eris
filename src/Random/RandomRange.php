<?php
namespace Eris\Random;

use RuntimeException;

/**
 * @return RandomRange
 */
function purePhpMtRand()
{
    return new RandomRange(new MersenneTwister());
}

// TODO: Extract Interface
class RandomRange
{
    /**
     * Rejection sampling needs fewer than two attempts on average,
     * so reaching this limit means that the source is broken.
     */
    private const MAXIMUM_ATTEMPTS = 100;

    private $source;
    
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @return void
     */
    public function seed($seed)
    {
        $this->source->seed($seed);
    }

    /**
     * Return a random number.
     * If $lower and $upper are specified, the number will fall into their
     * inclusive range.
     * Otherwise the number from the source will be directly returned.
     *
     * @param integer|null $lower
     * @param integer|null $upper
     * @return integer
     * @throws RuntimeException when the source does not produce a number inside the range
     */
    public function rand($lower = null, $upper = null)
    {
        if ($lower === null && $upper === null) {
            return $this->source->extractNumber();
        }

        if ($lower > $upper) {
            list($lower, $upper) = [$upper, $lower];
        }
        if (is_int($lower) && is_int($upper)) {
            // $upper - $lower would overflow into a float
            if ($lower < 0 && $upper > PHP_INT_MAX + $lower) {
                return $this->randInRangeWiderThanPhpIntMax($lower, $upper);
            }
            if ($upper - $lower > $this->source->max()) {
                return $this->randInRangeWiderThanSource($lower, $upper);
            }
        }
        $delta = $upper - $lower;
        $divisor = ($this->source->max()) / ($delta + 1);

        for ($attempts = 0; $attempts < self::MAXIMUM_ATTEMPTS; $attempts++) {
            $retval = (int) floor($this->source->extractNumber() / $divisor);
            if ($retval <= $delta) {
                return $retval + $lower;
            }
        }

        throw $this->tooManyAttempts($lower, $upper);
    }

    /**
     * More than half of all integers are inside the range,
     * so drawing any integer and rejecting it when it lies outside is cheap.
     *
     * @param integer $lower
     * @param integer $upper
     * @return integer
     */
    private function randInRangeWiderThanPhpIntMax($lower, $upper)
    {
        for ($attempts = 0; $attempts < self::MAXIMUM_ATTEMPTS; $attempts++) {
            $value = $this->randomBits();
            if ($value >= $lower && $value <= $upper) {
                return $value;
            }
        }

        throw $this->tooManyAttempts($lower, $upper);
    }

    /**
     * @param integer $lower
     * @param integer $upper
     * @return integer
     */
    private function randInRangeWiderThanSource($lower, $upper)
    {
        $delta = $upper - $lower;

        // the smallest 2^n - 1 that is not less than $delta
        $mask = $delta;
        for ($shift = 1; $shift < 64; $shift *= 2) {
            $mask |= $mask >> $shift;
        }

        for ($attempts = 0; $attempts < self::MAXIMUM_ATTEMPTS; $attempts++) {
            $offset = $this->randomBits() & $mask;
            if ($offset <= $delta) {
                return $lower + $offset;
            }
        }

        throw $this->tooManyAttempts($lower, $upper);
    }

    /**
     * 64 random bits from three numbers of the source. Every source shipped
     * with Eris returns at least 31 random bits, so every bit of the result
     * is covered by at least one random bit.
     *
     * @return integer
     */
    private function randomBits()
    {
        return ($this->source->extractNumber() << 33)
            ^ ($this->source->extractNumber() << 2)
            ^ $this->source->extractNumber();
    }

    /**
     * @param integer|float $lower
     * @param integer|float $upper
     * @return RuntimeException
     */
    private function tooManyAttempts($lower, $upper)
    {
        return new RuntimeException(
            'Could not generate a random number between ' . var_export($lower, true) .
            ' and ' . var_export($upper, true) . ' in ' . self::MAXIMUM_ATTEMPTS . ' attempts. ' .
            'The random source ' . get_class($this->source) . ' should return ' .
            'uniformly distributed integers between 0 and ' . var_export($this->source->max(), true)
        );
    }
}
