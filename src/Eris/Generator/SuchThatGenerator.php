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
        $input = $this->generator->__invoke($size);
        while (!call_user_func($this->filter, $input->unbox())) {
            $input = $this->generator->__invoke($size);
        }
        return $input;
    }

    public function shrink(GeneratedValue $value)
    {
        $shrunk = $this->generator->shrink($value);
        while (!call_user_func($this->filter, $shrunk->unbox())) {
            $shrunk = $this->generator->shrink($shrunk);
        }
        return $shrunk;
    }

    public function contains(GeneratedValue $value)
    {
        // TODO: duplication of call_user_func
        return $this->generator->contains($value)
            && call_user_func($this->filter, $value->unbox());
    }
}
