<?php
namespace Eris\Generator;

use Eris\Generator;

// TODO: support calls like ($function . $generator)
// TODO: $function vs $map, choose a name (look at test.check)
function map(callable $function, Generator $generator)
{
    return new MapGenerator($function, $generator);
}

class MapGenerator implements Generator
{
    private $map;
    private $generator;
    
    public function __construct($map, $generator)
    {
        $this->map = $map;
        $this->generator = $generator;
    }

    public function __invoke($_size)
    {
        $input = $this->generator->__invoke($_size);
        $value = call_user_func($this->map, $input);
        return GeneratedValue::fromValueAndInput(
            $value,
            $input
        );
    }

    public function shrink($value)
    {
        throw new \BadMethodCallException("Not implemented yet");
    }

    public function contains($value)
    {
        throw new \BadMethodCallException("Not implemented yet");
    }
}
