<?php
namespace Eris\Generator;

use Eris\Generator;

// TODO: support calls like ($function . $generator)
function map(callable $function, Generator $generator)
{
    return new MapGenerator($function, $generator);
}

class MapGenerator implements Generator
{
    private $map;
    private $generator;
    
    public function __construct(callable $map, $generator)
    {
        $this->map = $map;
        $this->generator = $generator;
    }

    public function __invoke($_size)
    {
        $input = $this->generator->__invoke($_size);
        return $input->map(
            $this->map,
            'map'
        );
    }

    public function shrink(GeneratedValue $value)
    {
        $input = $value->input();
        $shrunkInput = $this->generator->shrink($input);
        return $shrunkInput->map(
            $this->map,
            'map'
        );
    }

    public function contains(GeneratedValue $value)
    {
        return $this->generator->contains($value->input());
    }
}
