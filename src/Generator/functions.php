<?php
namespace Eris\Generator;

use Eris\Generator;

function ensureAreAllGenerators(array $generators): array
{
    return array_map('Eris\Generator\ensureIsGenerator', $generators);
}

function ensureIsGenerator($generator): \Eris\Generator|\Eris\Generator\ConstantGenerator
{
    if ($generator instanceof Generator) {
        return $generator;
    }
    return new ConstantGenerator($generator);
}
