<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

/**
 * One Generator for each member of the Tuple:
 * tuple(Generator, Generator, Generator...)
 * Or an array of generators:
 * tuple(array $generators)
 * @return Generator\TupleGenerator
 */
function tuple()
{
    $arguments = func_get_args();
    if (is_array($arguments[0])) {
        $generators = $arguments[0];
    } else {
        $generators = $arguments;
    }
    return new TupleGenerator($generators);
}

class TupleGenerator implements Generator
{
    private $generators;
    private $size;
    private $numberOfGenerators;

    public function __construct(array $generators)
    {
        $this->generators = ensureAreAllGenerators($generators);
        $this->numberOfGenerators = count($generators);
    }

    public function __invoke($size, $rand)
    {
        $input = array_map(
            function ($generator) use ($size, $rand) {
                return $generator($size, $rand);
            },
            $this->generators
        );
        return GeneratedValueSingle::fromValueAndInput(
            array_map(
                function ($value) {
                    return $value->unbox();
                },
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
        if (count($generators) == 1) {
            return $options;
        } else {
            return $options->cartesianProduct(
                $this->optionsFromTheseGenerators(
                    array_slice($generators, 1),
                    array_slice($inputSubset, 1)
                ),
                function ($first, $second) {
                    return array_merge($first, $second);
                }
            );
        }
    }

    public function shrink(GeneratedValueSingle $tuple)
    {
        $input = $tuple->input();

        return $this->optionsFromTheseGenerators($this->generators, $input)
            ->remove($tuple);
    }

    private function ensureAreAllGenerators(array $generators)
    {
        return array_map(
            function ($generator) {
                if ($generator instanceof Generator) {
                    return $generator;
                }
                return new Constant($generator);
            },
            $generators
        );
    }

    private function domainsTupleAsString()
    {
        $domainOfElements = '(';
        foreach ($this->generators as $generator) {
            $domainOfElements .= get_class($generator);
            $domainOfElements .= ',';
        }
        return substr($domainOfElements, 0, -1) . ')';
    }
}
