<?php
namespace Eris\Generator;
use Eris\Generator;
use InvalidArgumentException;
use Exception;

function oneOf(array $generators)
{
    if (empty($generators)) {
        throw new InvalidArgumentException(
            'Generator\OneOf cannot choose from an empty array of generators'
        );
    }
    return frequency(array_map(
        function($generator) {
            return [1, $generator];
        },
        $generators
    ));
}

function frequency(array $generatorsWithFrequency)
{
    list($frequencies, $generators) = array_reduce(
        $generatorsWithFrequency,
        function($result, $generatorWithFrequency) {
            $result[0][] = ensureIsFrequency($generatorWithFrequency[0]);
            $result[1][] = ensureIsGenerator($generatorWithFrequency[1]);
            return $result;
        },
        [[], []]
    );

    $acc = 0;
    $random = rand(0, array_sum($frequencies));
    foreach ($frequencies as $key => $frequency) {
        $acc += $frequency;
        if ($random <= $acc) {
            return $generators[$key];
        }
    }

    throw new Exception(
        'Unable to choose a generator with frequencies: ' . var_export($frequencies, true)
    );
}

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

function ensureIsFrequency($frequency)
{
    if (!is_int($frequency) || $frequency <= 0) {
        throw new InvalidArgumentException(
            'Frequency must be an integer greater than 0, given: ' . var_export($frequency, true)
        );
    }
    return $frequency;
}
