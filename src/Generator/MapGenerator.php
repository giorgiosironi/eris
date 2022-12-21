<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

// TODO: support calls like ($function . $generator)
function map(callable $function, Generator $generator)
{
    return Generators::map($function, $generator);
}

/**
 * @psalm-template T
 * @template-implements Generator<T>
 */
class MapGenerator implements Generator
{
    private $map;
    private $generator;
    
    public function __construct(callable $map, $generator)
    {
        $this->map = $map;
        $this->generator = $generator;
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $input = $this->generator->__invoke($_size, $rand);
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
}
