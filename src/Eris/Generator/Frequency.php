<?php
namespace Eris\Generator;
use Eris\Generator;
use DomainException;
use InvalidArgumentException;

function frequency(array $generatorsWithFrequency)
{
    return new Frequency($generatorsWithFrequency);
}

class Frequency implements Generator
{
    private $generators;

    public function __construct(array $generatorsWithFrequency)
    {
        if (empty($generatorsWithFrequency)) {
            throw new InvalidArgumentException(
                'Cannot choose from an empty array of generators'
            );
        }
        $this->generators = array_map(
            function($generatorWithFrequency) {
                return [
                    'generator' =>  ensureIsGenerator($generatorWithFrequency[1]),
                    'frequency' => $this->ensureIsFrequency($generatorWithFrequency[0]),
                ];
            },
            $generatorsWithFrequency
        );
    }

    public function __invoke()
    {
        return $this->pickFrom($this->generators)->__invoke();
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            return new DomainException(
                var_export($element, true) . ' is not in one of the given domains'
            );
        }
        return $this->pickFrom($this->allGeneratorsAbleToShrink($element))->shrink($element);
    }

    public function contains($element)
    {
        foreach ($this->generators as $generator) {
            if ($generator['generator']->contains($element)) {
                return true;
            }
        }
        return false;
    }

    private function allGeneratorsAbleToShrink($element)
    {
        return array_filter(
            $this->generators,
            function($generator) use ($element) {
                return $generator['generator']->contains($element);
            }
        );
    }

    private function pickFrom($generators)
    {
        $acc = 0;
        $random = rand(0, array_sum($this->frequenciesFrom($generators)));
        foreach ($generators as $generator) {
            $acc += $generator['frequency'];
            if ($random <= $acc) {
                return $generator['generator'];
            }
        }
        throw new Exception(
            'Unable to pick a generator with frequencies: ' . var_export($this->frequencies, true)
        );
    }

    private function frequenciesFrom($generators)
    {
        return array_map(
            function($generatorWithFrequency) {
                return $generatorWithFrequency['frequency'];
            },
            $generators
        );
    }

    private function ensureIsFrequency($frequency)
    {
        if (!is_int($frequency) || $frequency <= 0) {
            throw new InvalidArgumentException(
                'Frequency must be an integer greater than 0, given: ' . var_export($frequency, true)
            );
        }
        return $frequency;
    }
}
