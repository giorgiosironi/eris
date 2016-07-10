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
        return GeneratedValue::fromValueAndInput(
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
            $options[] = GeneratedValue::fromValueAndInput(
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
                function($first, $second) {
                    return array_merge($first, $second);
                }
            );
        }
    }

    public function shrink(GeneratedValue $tuple)
    {
        $this->checkValueToShrink($tuple);
        $input = $tuple->input();


        return $this->optionsFromTheseGenerators($this->generators, $input)
            ->remove($tuple);
        
        if (count($inputs) == 1) {
            return GeneratedValue::fromValueAndInput(
                array_map(
                    function ($element) { return $element->unbox(); },
                    $input
                ),
                $input,
                'tuple'
            );
        } else {
            $values = [];
            foreach ($inputs as $optionInput) {
                $values[] = GeneratedValue::fromValueAndInput(
                    array_map(
                        function ($element) { return $element->unbox(); },
                        $optionInput
                    ),
                    $optionInput,
                    'tuple'
                );
            }
            return new GeneratedValueOptions($values);
        }
    }

    public function contains(GeneratedValue $tuple)
    {
        $input = $tuple->input();
        if (!is_array($input)) {
            throw new \Exception("Input must be an array, not " . var_export($input, true));
        }
        if (count($input) !== $this->numberOfGenerators) {
            return false;
        }
        for ($i = 0; $i < $this->numberOfGenerators; $i++) {
            if (!$this->generators[$i]->contains($input[$i])) {
                return false;
            }
        }
        return true;
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

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new DomainException(
                'Cannot shrink ' . var_export($value, true) . ' because it does not ' .
                ' belong to the domain of the Tuples with domain elements ' .
                $this->domainsTupleAsString()
            );
        }
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
