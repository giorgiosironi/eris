<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

function seq(Generator $singleElementGenerator)
{
    return Generators::seq($singleElementGenerator);
}

class SequenceGenerator implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size, RandomRange $rand)
    {
        $sequenceLength = $rand->rand(0, $size);
        return $this->vector($sequenceLength)->__invoke($size, $rand);
    }

    public function shrink(GeneratedValue $sequence)
    {
        $options = [];
        if (count($sequence->unbox()) > 0) {
            $options[] = $this->shrinkInSize($sequence);
            // TODO: try to shrink the elements also of longer sequences
            if (count($sequence->unbox()) < 10) {
                // a size which is computationally acceptable
                $shrunkElements = $this->shrinkTheElements($sequence);
                foreach ($shrunkElements as $shrunkValue) {
                    $options[] = $shrunkValue;
                }
            }
        }

        return new GeneratedValueOptions($options);
    }

    private function shrinkInSize($sequence)
    {
        if (count($sequence->unbox()) === 0) {
            return $sequence;
        }

        $input = $sequence->input();
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        $input = array_values($input);
        return GeneratedValueSingle::fromValueAndInput(
            array_map(
                function ($element) {
                    return $element->unbox();
                },
                $input
            ),
            $input,
            'sequence'
        );
    }

    /**
     * @return GeneratedValueOptions
     */
    private function shrinkTheElements($sequence)
    {
        return $this->vector(count($sequence->unbox()))->shrink($sequence);
    }

    private function vector($size)
    {
        return new VectorGenerator($size, $this->singleElementGenerator);
    }
}
