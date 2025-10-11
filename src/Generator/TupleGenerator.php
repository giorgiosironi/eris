<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * One Generator for each member of the Tuple:
 * tuple(Generator, Generator, Generator...)
 * Or an array of generators:
 * tuple(array $generators)
 * @return Generator\TupleGenerator
 */
function tuple(): mixed
{
    return call_user_func_array(
        Generators::tuple(...),
        func_get_args()
    );
}

/**
 * @template-implements Generator<list<mixed>>
 */
class TupleGenerator implements Generator
{
    private $generators;

    public function __construct(array $generators)
    {
        $this->generators = ensureAreAllGenerators($generators);
    }

    public function __invoke($size, RandomRange $rand)
    {
        $input = array_map(
            fn($generator) => $generator($size, $rand),
            $this->generators
        );
        return GeneratedValueSingle::fromValueAndInput(
            array_map(
                fn($value) => $value->unbox(),
                $input
            ),
            $input,
            // TODO: sometimes this should be 'vector'
            // due to delegation?
            'tuple'
        );
    }

    /**
     * TODO: recursion may cause problems here as other Generators
     * like Vector use this with a high number of elements.
     * Rewrite to something that does not overflow the stack
     * @return GeneratedValueOptions
     */
    private function optionsFromTheseGenerators($generators, $inputSubset)
    {
        $optionsForThisElement = $generators[0]->shrink($inputSubset[0]);
        // so that it can be used in combination with other shrunk elements
        $optionsForThisElement = $optionsForThisElement->add($inputSubset[0]);
        $options = [];
        foreach ($optionsForThisElement as $value) {
            $options[] = GeneratedValueSingle::fromValueAndInput(
                [$value->unbox()],
                [$value],
                'tuple'
            );
        }
        $options = new GeneratedValueOptions($options);
        if (count($generators) === 1) {
            return $options;
        }
        return $options->cartesianProduct(
            $this->optionsFromTheseGenerators(
                array_slice($generators, 1),
                array_slice($inputSubset, 1)
            ),
            fn($first, $second): array => array_merge($first, $second)
        );
    }

    public function shrink(GeneratedValue $tuple)
    {
        $input = $tuple->input();

        return $this->optionsFromTheseGenerators($this->generators, $input)
            ->remove($tuple);
    }
}
