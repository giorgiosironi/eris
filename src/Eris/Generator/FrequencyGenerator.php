<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;
use InvalidArgumentException;

/**
 * @return FrequencyGenerator
 */
function frequency(/*$frequencyAndGenerator, $frequencyAndGenerator, ...*/)
{
    return new FrequencyGenerator(func_get_args());
}

class FrequencyGenerator implements Generator
{
    private $generators;

    public function __construct(array $generatorsWithFrequency)
    {
        if (empty($generatorsWithFrequency)) {
            throw new InvalidArgumentException(
                'Cannot choose from an empty array of generators'
            );
        }
        $this->generators = array_reduce(
            $generatorsWithFrequency,
            function($generators, $generatorWithFrequency) {
                list($frequency, $generator) = $generatorWithFrequency;
                $frequency = $this->ensureIsFrequency($generatorWithFrequency[0]);
                $generator = ensureIsGenerator($generatorWithFrequency[1]);
                if ($frequency > 0) {
                    $generators[] = [
                        'generator' => $generator,
                        'frequency' => $frequency,
                    ];
                }
                return $generators;
            },
            []
        );
    }

    public function __invoke($size, $rand)
    {
        list ($index, $generator) = $this->pickFrom($this->generators, $rand);
        $originalValue = $generator->__invoke($size, $rand);
        return GeneratedValue::fromValueAndInput(
            $originalValue->unbox(),
            [
                'value' => $originalValue,
                'generator' => $index,
            ],
            'frequency'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                var_export($element, true) . ' is not in one of the given domains'
            );
        }
        $input = $element->input();
        $originalGeneratorIndex = $input['generator'];
        $shrinkedValue = $this->generators[$originalGeneratorIndex]['generator']->shrink($input['value']);

        return GeneratedValue::fromValueAndInput(
            $shrinkedValue->unbox(),
            [
                'value' => $shrinkedValue,
                'generator' => $originalGeneratorIndex,
            ],
            'frequency'
        );
    }

    public function contains(GeneratedValue $element)
    {
        $input = $element->input();
        $originalGeneratorIndex = $input['generator'];
        return $this->generators[$originalGeneratorIndex]['generator']->contains($input['value']);
    }

    /**
     * @return array  two elements: index and Generator object
     */
    private function pickFrom($generators, $rand)
    {
        $acc = 0;
        $random = $rand(1, array_sum($this->frequenciesFrom($generators)));
        foreach ($generators as $index => $generator) {
            $acc += $generator['frequency'];
            if ($random <= $acc) {
                return [$index, $generator['generator']];
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
        if (!is_int($frequency) || $frequency < 0) {
            throw new InvalidArgumentException(
                'Frequency must be an integer greater or equal than 0, given: ' . var_export($frequency, true)
            );
        }
        return $frequency;
    }
}
