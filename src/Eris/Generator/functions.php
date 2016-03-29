<?php
namespace Eris\Generator;

use Eris\Generator;

function ensureAreAllGenerators(array $generators)
{
    return array_map('Eris\Generator\ensureIsGenerator', $generators);
}

function ensureIsGenerator($generator)
{
    if ($generator instanceof Generator) {
        return $generator;
    }
    return new ConstantGenerator($generator);
}
