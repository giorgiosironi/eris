<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;
use InvalidArgumentException;

function frequency(array $generatorsWithFrequency)
{
    return new FrequencyGenerator($generatorsWithFrequency);
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

    public function __invoke($size)
    {
        list ($index, $generator) = $this->pickFrom($this->generators);
        $originalValue = $generator->__invoke($size);
        return $originalValue
            ->derivedIn('frequency')
            ->annotate('original_generator', $index);
    }

    public function shrink(GeneratedValue $element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                var_export($element, true) . ' is not in one of the given domains'
            );
        }
        $originalGeneratorIndex = $element->annotation('original_generator');
        $shrinkedValue = $this->generators[$originalGeneratorIndex]['generator']->shrink($element->input());

        return $shrinkedValue
            ->derivedIn('frequency')
            ->annotate('original_generator', $originalGeneratorIndex);
    }

    public function contains(GeneratedValue $element)
    {
        $originalGeneratorIndex = $element->annotation('original_generator');
        return $this->generators[$originalGeneratorIndex]['generator']->contains($element->input());
    }

    /**
     * @return array  two elements: index and Generator object
     */
    private function pickFrom($generators)
    {
        $acc = 0;
        $random = rand(1, array_sum($this->frequenciesFrom($generators)));
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
