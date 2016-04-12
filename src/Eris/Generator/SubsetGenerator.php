<?php
namespace Eris\Generator;

// TODO: dependency on ForAll is bad,
// maybe inject the relative size?
use Eris\Quantifier\ForAll;
use Eris\Generator;

/**
 * @param array $universe
 * @return SubsetGenerator
 */
function subset($input)
{
    return new SubsetGenerator($input);
}

class SubsetGenerator implements Generator
{
    private $universe;
    
    public function __construct(array $universe)
    {
        $this->universe = $universe;
    }

    public function __invoke($size, $rand)
    {
        $relativeSize = $size / ForAll::DEFAULT_MAX_SIZE;
        $maximumSubsetIndex = floor(pow(2, count($this->universe)) * $relativeSize);
        $subsetIndex = $rand(0, $maximumSubsetIndex);
        $binaryDescription = str_pad(decbin($subsetIndex), count($this->universe), "0", STR_PAD_LEFT);
        $subset = [];
        for ($i = 0; $i < strlen($binaryDescription); $i++) {
            $elementPresent = $binaryDescription{$i};
            if ($elementPresent == "1") {
                $subset[] = $this->universe[$i];
            } 
        }

        return GeneratedValue::fromJustValue($subset, 'subset');
    }

    public function shrink(GeneratedValue $set)
    {
        // TODO: see SetGenerator::shrink()
        if (!$this->contains($set)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($set, true) . '} because ' .
                'it does not belong to the domain of this set'
            );
        }

        if (count($set->unbox()) === 0) {
            return $set;
        }

        $input = $set->input();
        // TODO: make deterministic by returning an array of GeneratedValues
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        return GeneratedValue::fromJustValue(
            array_values($input),
            'subset'
        );
    }

    public function contains(GeneratedValue $set)
    {
        foreach ($set->input() as $element) {
            if (!in_array($element, $this->universe)) {
                return false;
            }
        }
        return true;
    }
}
