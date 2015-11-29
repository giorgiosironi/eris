<?php
namespace Eris\Generator;

// TODO: dependency on ForAll is bad,
// maybe inject the relative size?
use Eris\Quantifier\ForAll;
use Eris\Generator;

class SubsetGenerator implements Generator
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

    public function shrink($set)
    {
        // TODO: see SetGenerator::shrink()
        if (!$this->contains($set)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($set, true) . '} because ' .
                'it does not belong to the domain of this set'
            );
        }

        if (count($set) === 0) {
            return $set;
        }

        $indexOfElementToRemove = array_rand($set);
        unset($set[$indexOfElementToRemove]);
        return array_values($set);
    }

    public function contains($set)
    {
        foreach ($set as $element) {
            if (!in_array($element, $this->universe)) {
                return false;
            }
        }
        return true;
    }
}
