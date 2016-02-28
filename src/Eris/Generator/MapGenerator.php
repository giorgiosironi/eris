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
        $value = $this->apply($input);
        return GeneratedValue::fromValueAndInput(
            $value,
            $input,
            'map'
        );
    }

    public function shrink(GeneratedValue $value)
    {
        $input = $value->input();
        $shrunkInput = $this->generator->shrink($input);
        return GeneratedValue::fromValueAndInput(
            $this->apply($shrunkInput),
            $shrunkInput,
            'map'
        );
    }

    private function apply(GeneratedValue $input)
    {
        return call_user_func($this->map, $input->unbox());
    }

    public function contains(GeneratedValue $value)
    {
        $input = $value->input();
        return $this->generator->contains($input);
    }
}
