<?php
namespace Eris\Random;

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
     */
    public function rand($lower = null, $upper = null)
    {
        if ($lower === null && $upper === null) {
            return $this->source->extractNumber();
        }

        $delta = $upper - $lower;
        $divisor = ($this->source->max()) / ($delta + 1);

        do {
            $retval = (int) floor($this->source->extractNumber() / $divisor);
        } while ($retval > $delta);

        return $retval + $lower;
    }
}
