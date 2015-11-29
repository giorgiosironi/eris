<?php
namespace Eris\Generator;

// TODO: dependency on ForAll is bad,
// maybe inject the relative size?
use Eris\Quantifier\ForAll;

class SubsetGenerator
{
    private $universe;
    
    public function __construct(array $universe)
    {
        $this->universe = $universe;
    }

    public function __invoke($size)
    {
        $relativeSize = $size / ForAll::DEFAULT_MAX_SIZE;
        $maximumSubsetIndex = floor(pow(2, count($this->universe)) * $relativeSize);
        $subsetIndex = rand(0, $maximumSubsetIndex);
        $binaryDescription = str_pad(decbin($subsetIndex), count($this->universe), "0", STR_PAD_LEFT);
        $subset = [];
        for ($i = 0; $i < strlen($binaryDescription); $i++) {
            $elementPresent = $binaryDescription{$i};
            if ($elementPresent == "1") {
                $subset[] = $this->universe[$i];
            } 
        }

        return $subset;
    }
}
