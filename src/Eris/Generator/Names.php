<?php
namespace Eris\Generator;

use Eris\Generator;

function names()
{
    return Names::defaultDataSet();
}

class Names implements Generator
{
    private $list;

    /**
     * @link http://data.bfontaine.net/names/firstnames.txt
     */
    public static function defaultDataSet()
    {
        return new self(
            array_map(
                function($line) {
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

    public function __invoke($_size)
    {
        // TODO: size is not used but it should
        $index = rand(0, count($this->list) - 1);
        return $this->list[$index];
    }

    public function shrink($value)
    {
        $candidateNames = $this->slightlyShorterNames($value);
        if (!$candidateNames) {
            return $value;
        }
        $distances = $this->distancesBy($value, $candidateNames);
        return $this->minimumDistanceName($distances);
    }

    public function contains($value)
    {
        return in_array($value, $this->list);
    }

    private function slightlyShorterNames($value)
    {
        $length = strlen($value);
        $lowerLength = $length - 1;
        return array_filter(
            $this->list,
            function($name) use ($lowerLength) {
                return strlen($name) == $lowerLength;
            }
        );
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
            function($distance) use ($minimumDistance) {
                return $distance == $minimumDistance;
            }
        );
        return array_keys($candidatesWithEqualDistance)[0];
    }
}
