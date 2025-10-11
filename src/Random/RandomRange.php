<?php
namespace Eris\Random;

function purePhpMtRand(): \Eris\Random\RandomRange
{
    return new RandomRange(new MersenneTwister());
}

// TODO: Extract Interface
class RandomRange
{
    public function __construct(private $source)
    {
    }

    public function seed($seed): void
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
     */
    public function rand($lower = null, $upper = null)
    {
        if ($lower === null && $upper === null) {
            return $this->source->extractNumber();
        }

        if ($lower > $upper) {
            [$lower, $upper] = [$upper, $lower];
        }
        $delta = $upper - $lower;
        $divisor = ($this->source->max()) / ($delta + 1);

        do {
            $retval = (int) floor($this->source->extractNumber() / $divisor);
        } while ($retval > $delta);

        return $retval + $lower;
    }
}
