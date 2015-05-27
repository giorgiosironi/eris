<?php
namespace Eris\Generator;

use Eris\Generator;
use InvalidArgumentException;
use Exception;

function ensureAreAllGenerators(array $generators)
{
    return array_map('Eris\Generator\ensureIsGenerator', $generators);
}

function ensureIsGenerator($generator)
{
    if ($generator instanceof Generator) {
        return $generator;
    }
    return new Constant($generator);
}
