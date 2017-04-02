<?php
namespace Eris\Generator;

use Eris\Generator;

/**
 * @return OneOfGenerator
 */
function oneOf(/*$a, $b, ...*/)
{
    return new OneOfGenerator(func_get_args());
}

class OneOfGenerator implements Generator
{
    private $generator;

    public function __construct($generators)
    {
        $this->generator = new FrequencyGenerator($this->allWithSameFrequency($generators));
    }

    public function __invoke($size, $rand)
    {
        return $this->generator->__invoke($size, $rand);
    }

    public function shrink(GeneratedValueSingle $element)
    {
        return $this->generator->shrink($element);
    }

    private function allWithSameFrequency($generators)
    {
        return array_map(
            function ($generator) {
                return [1, $generator];
            },
            $generators
        );
    }
}
