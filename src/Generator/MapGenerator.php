<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

// TODO: support calls like ($function . $generator)

class MapGenerator implements Generator
{
    private $mapFn;
    private $generator;
    
    public function __construct(callable $map, $generator)
    {
        $this->mapFn = $map;
        $this->generator = $generator;
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $input = $this->generator->__invoke($_size, $rand);
        return $input->map(
            $this->mapFn,
            'map'
        );
    }

    public function shrink(GeneratedValue $value)
    {
        $input = $value->input();
        $shrunkInput = $this->generator->shrink($input);
        return $shrunkInput->map(
            $this->mapFn,
            'map'
        );
    }

    public static function map(callable $function, Generator $generator)
    {
        return new MapGenerator($function, $generator);
    }
}
