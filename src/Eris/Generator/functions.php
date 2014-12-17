<?php
namespace Eris\Generator;
use Eris\Generator;
use InvalidArgumentException;

function oneOf(array $generators)
{
    if (empty($generators)) {
        throw new InvalidArgumentException(
            'Generator\OneOf cannot choose from an empty array of generators'
        );
    }
    $generators = ensureAreAllGenerators($generators);
    return $generators[array_rand($generators)];
}

function ensureAreAllGenerators(array $generators)
{
    return array_map(
        function($generator) {
            if ($generator instanceof Generator) {
                return $generator;
            }
            return new Constant($generator);
        },
        $generators
    );
}
