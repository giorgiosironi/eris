<?php
namespace Eris\Generator;

use Eris\Generator;

function bind($value, callable $generatorFactory)
{
    return new BindGenerator();
}

class BindGenerator implements Generator
{
    public function __invoke($size)
    {
    }

    public function shrink(GeneratedValue $element){}

    public function contains(GeneratedValue $element){}
}
