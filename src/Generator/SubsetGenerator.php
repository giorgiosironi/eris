<?php
namespace Eris\Generator;

// TODO: dependency on ForAll is bad,
// maybe inject the relative size?
use Eris\Generators;
use Eris\Quantifier\ForAll;
use Eris\Random\RandomRange;
use Eris\Generator;

/**
 * @param array $input
 * @return SubsetGenerator
 */
function subset($input)
{
    return Generators::subset($input);
}

class SubsetGenerator implements Generator
{
    private $universe;
    
    public function __construct(array $universe)
    {
        $this->universe = $universe;
    }

    public function __invoke($size, RandomRange $rand)
    {
        $relativeSize = $size / ForAll::DEFAULT_MAX_SIZE;
        $maximumSubsetIndex = floor((2 ** count($this->universe)) * $relativeSize);
        $subsetIndex = $rand->rand(0, (int)$maximumSubsetIndex);
        $binaryDescription = str_pad(decbin($subsetIndex), count($this->universe), "0", STR_PAD_LEFT);
        $subset = [];
        for ($i = 0, $iMax = strlen($binaryDescription); $i < $iMax; $i++) {
            $elementPresent = $binaryDescription[$i];
            if ($elementPresent == "1") {
                $subset[] = $this->universe[$i];
            }
        }

        return GeneratedValueSingle::fromJustValue($subset, 'subset');
    }

    public function shrink(GeneratedValue $element)
    {
        // TODO: see SetGenerator::shrink()
        if (count($element->unbox()) === 0) {
            return $element;
        }

        $input = $element->input();
        // TODO: make deterministic by returning an array of GeneratedValues
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        return GeneratedValueSingle::fromJustValue(
            array_values($input),
            'subset'
        );
    }
}
