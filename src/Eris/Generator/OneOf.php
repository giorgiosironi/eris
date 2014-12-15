<?php
namespace Eris\Generator;
use Eris\Generator;
use DomainException;
use InvalidArgumentException;

function oneOf(array $generators)
{
    return new OneOf($generators);
}

class OneOf implements Generator
{
    private $generators;

    public function __construct(array $generators)
    {
        if (empty($generators)) {
            throw new InvalidArgumentException(
                'Generator\OneOf cannot choose from an empty array of generators'
            );
        }
        $this->generators = $this->ensureAreAllGenerators($generators);
    }

    public function __invoke()
    {
        $indexOfChosenGenerator = array_rand($this->generators);
        return $this->generators[$indexOfChosenGenerator]->__invoke();
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw $this->elementNotInDomain($element);
        }

        list($lastUsedGeneratorIndex, $lastUsedGenerator) = $this->lastGeneratorAbleToShrink($element);

        $willShrinkToEarlierGenerator = (new Boolean())->__invoke() && $lastUsedGeneratorIndex > 0;

        if ($willShrinkToEarlierGenerator) {
            return $this->generators[$lastUsedGeneratorIndex - 1]->__invoke();
        }
        return $this->generators[$lastUsedGeneratorIndex]->shrink($element);
    }

    public function contains($element)
    {
        foreach ($this->generators as $generator) {
            if ($generator->contains($element)) {
                return true;
            }
        }
        return false;
    }

    // TODO: duplicated from Generator/Tuple
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

    private function lastGeneratorAbleToShrink($element)
    {
        for ($i = count($this->generators) - 1; $i >= 0; $i--) {
            if ($this->generators[$i]->contains($element)) {
                return [$i, $this->generators[$i]];
            }
        }
        throw $this->elementNotInDomain();
    }

    private function elementNotInDomain($element)
    {
        return new DomainException(
            var_export($element, true) . ' in not in one of the given domains'
        );
    }
}
