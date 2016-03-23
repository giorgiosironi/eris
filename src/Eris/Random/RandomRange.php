<?php
namespace Eris\Random;

class RandomRange
{
    private $source;
    
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @return integer
     */
    public function rand($lower = null, $upper = null)
    {
        if ($lower === null && $upper === null) {
            return $this->source->extractNumber();
        }

        $delta = $upper - $lower;
        $divisor = $this->source->max() / ($delta + 1);

        do { 
            $retval = (int) floor($this->source->extractNumber() / $divisor);
        } while ($retval > $delta);

        return $retval + $lower;
    }
}
