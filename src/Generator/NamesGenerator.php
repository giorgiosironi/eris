<?php
namespace Eris\Generator;

use Eris\Generator;

function names()
{
    return NamesGenerator::defaultDataSet();
}

class NamesGenerator implements Generator
{
    private $list;

    /**
     * @link http://data.bfontaine.net/names/firstnames.txt
     */
    public static function defaultDataSet()
    {
        return new self(
            array_map(
                function ($line) {
                    return trim($line, " \n");
                },
                file(__DIR__ . "/first_names.txt")
            )
        );
    }

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function __invoke($size, $rand)
    {
        $candidateNames = $this->filterDataSet(
            $this->lengthLessThanOrEqualTo($size)
        );
        if (!$candidateNames) {
            return GeneratedValueSingle::fromJustValue('', 'names');
        }
        $index = $rand(0, count($candidateNames) - 1);
        return GeneratedValueSingle::fromJustValue($candidateNames[$index], 'names');
    }

    public function shrink(GeneratedValueSingle $value)
    {
        $candidateNames = $this->filterDataSet(
            $this->lengthSlightlyLessThan(strlen($value->unbox()))
        );

        if (!$candidateNames) {
            return $value;
        }
        $distances = $this->distancesBy($value->unbox(), $candidateNames);
        return GeneratedValueSingle::fromJustValue($this->minimumDistanceName($distances), 'names');
    }

    private function filterDataSet(callable $predicate)
    {
        return array_values(array_filter(
            $this->list,
            $predicate
        ));
    }

    private function lengthLessThanOrEqualTo($size)
    {
        return function ($name) use ($size) {
            return strlen($name) <= $size;
        };
    }

    private function lengthSlightlyLessThan($size)
    {
        $lowerLength = $size - 1;
        return function ($name) use ($lowerLength) {
            return strlen($name) === $lowerLength;
        };
    }

    private function distancesBy($value, array $candidateNames)
    {
        $distances = [];
        foreach ($candidateNames as $name) {
            $distances[$name] = levenshtein($value, $name);
        }
        return $distances;
    }

    private function minimumDistanceName($distances)
    {
        $minimumDistance = min($distances);
        $candidatesWithEqualDistance = array_filter(
            $distances,
            function ($distance) use ($minimumDistance) {
                return $distance == $minimumDistance;
            }
        );
        return array_keys($candidatesWithEqualDistance)[0];
    }
}
