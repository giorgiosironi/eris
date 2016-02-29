<?php
namespace Eris\Generator;

use Eris\Generator;

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
    
    public function __construct(callable $filter, $generator)
    {
        $this->filter = $filter;
        $this->generator = $generator;
    }

    // TODO: termination conditions for while in __invoke() and shrink()
    public function __invoke($size)
    {
        $value = $this->generator->__invoke($size);
        while (!$this->predicate($value)) {
            $value = $this->generator->__invoke($size);
        }
        return $value;
    }

    public function shrink(GeneratedValue $value)
    {
        $shrunk = $this->generator->shrink($value);
        while (!$this->predicate($shrunk)) {
            $shrunk = $this->generator->shrink($shrunk);
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
