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

    public function __construct(array $generators)
    {
        $this->generators = ensureAreAllGenerators($generators);
        $this->size = count($generators);
    }

    public function __invoke($size)
    {
        $input = array_map(
            function($generator) use ($size) {
                return $generator($size);
            },
            $this->generators
        );
        return GeneratedValue::fromValueAndInput(
            array_map(
                function($value) {
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

    public function shrink($tuple)
    {
        // TODO: GeneratedValue in signature of shrink() and contains()
        if (!($tuple instanceof GeneratedValue)) {
            throw new \Exception("Value to be shrunk must be a GeneratedValue, not " . var_export($tuple, true));
        }
        $this->checkValueToShrink($tuple);
        $input = $tuple->input();

        if (count($input) > 0) {
            $attemptsToShrink = 10;
            $numberOfElementsToShrink = rand(1, max(floor($this->size/2), 1));

            while ($numberOfElementsToShrink > 0 && $attemptsToShrink > 0) {
                $indexOfElementToShrink = rand(0, $this->size - 1);

                $shrinkedValue = $this->generators[$indexOfElementToShrink]
                    ->shrink($input[$indexOfElementToShrink]);

                if ($shrinkedValue === $input[$indexOfElementToShrink]) {
                    $attemptsToShrink--;
                    continue;
                }
                $numberOfElementsToShrink--;
                $input[$indexOfElementToShrink] = $shrinkedValue;
            }
        }
        return GeneratedValue::fromValueAndInput(
            array_map(
                function($element) { return $element->unbox(); },
                $input
            ),
            $input, 
            'tuple'
        );
    }

    public function contains($tuple)
    {
        if (!($tuple instanceof GeneratedValue)) {
            throw new \Exception("Value to be shrunk must be a GeneratedValue, not " . var_export($tuple, true));
        }
        $input = $tuple->input();
        if (!is_array($input)) {
            throw new \Exception("Input must be an array, not " . var_export($input, true));
        }
        if (count($input) !== $this->size) {
            return false;
        }
        for ($i = 0; $i < $this->size; $i++) {
            if (!$this->generators[$i]->contains($input[$i])) {
                return false;
            }
        }
        return true;
    }

    private function ensureAreAllGenerators(array $generators)
    {
        return array_map(
            function($generator) {
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
