<?php
namespace Eris\Generator;

use Eris\Generator;
use LogicException;

/**
 * TODO: maybe filter?
 */
function suchThat(callable $filter, Generator $generator)
{
    return new SuchThatGenerator($filter, $generator);
}

class SuchThatGenerator implements Generator
{
    private $filter;
    private $generator;
    private $maximumAttempts;
    
    public function __construct(callable $filter, $generator)
    {
        $this->filter = $filter;
        $this->generator = $generator;
        $this->maximumAttempts = 10;
    }

    public function __invoke($size)
    {
        $value = $this->generator->__invoke($size);
        $attempts = 0;
        while (!$this->predicate($value)) {
            if ($attempts >= $this->maximumAttempts) {
                throw new LogicException("Tried to satisfy predicate $attempts times, but could not generate a good value. You should try to improve your generator to make it more likely to output good values, or to use a less restrictive condition. Last generated value was: " . $value);
            }
            $value = $this->generator->__invoke($size);
            $attempts++;
        }
        return $value;
    }

    public function shrink(GeneratedValue $value)
    {
        $shrunk = $this->generator->shrink($value);
        $attempts = 0;
        while (!$this->predicate($shrunk)) {
            if ($attempts >= $this->maximumAttempts) {
                return $value;
            }
            $shrunk = $this->generator->shrink($shrunk);
            $attempts++;
        }
        return $shrunk;
    }

    public function contains(GeneratedValue $value)
    {
        return $this->generator->contains($value)
            && $this->predicate($value);
    }

    private function predicate(GeneratedValue $value)
    {
        return call_user_func($this->filter, $value->unbox());
    }
}
