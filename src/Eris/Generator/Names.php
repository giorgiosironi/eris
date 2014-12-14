<?php
namespace Eris\Generator;
use Eris\Generator;

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

    public function __invoke()
    {
        $index = rand(0, count($this->list) - 1);
        return $this->list[$index];
    }

    public function shrink($value)
    {
        $length = strlen($value);
        $lowerLength = $length - 1;
        $candidateNames = array_filter(
            $this->list,
            function($name) use ($lowerLength) {
                return strlen($name) == $lowerLength;
            }
        ); 
        if (!$candidateNames) {
            return $value;
        }
        $distances = [];
        foreach ($candidateNames as $name) {
            $distances[$name] = levenshtein($value, $name);
        }
        $minimumDistance = min($distances);
        $candidatesWithEqualDistance = array_filter(
            $distances,
            function($distance) use ($minimumDistance) {
                return $distance == $minimumDistance;
            }
        );
        return array_keys($candidatesWithEqualDistance)[0];
    }

    public function contains($value)
    {
        return true;
        
    }
}
