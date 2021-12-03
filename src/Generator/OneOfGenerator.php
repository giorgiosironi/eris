<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @return OneOfGenerator
 */
function oneOf(/*$a, $b, ...*/)
{
    return call_user_func_array(
        [Generators::class, 'oneOf'],
        func_get_args()
    );
}

class OneOfGenerator implements Generator
{
    private $generator;

    public function __construct($generators)
    {
        $this->generator = new FrequencyGenerator($this->allWithSameFrequency($generators));
    }

    public function __invoke($size, RandomRange $rand)
    {
        return $this->generator->__invoke($size, $rand);
    }

    public function shrink(GeneratedValue $element)
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
