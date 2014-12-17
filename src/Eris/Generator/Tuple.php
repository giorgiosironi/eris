<?php
namespace Eris\Generator;
use Eris\Generator;

function tuple(array $generators)
{
    return new Tuple($generators);
}

class Tuple implements Generator
{
    private $generators;
    private $size;

    public function __construct(array $generators)
    {
        $this->generators = ensureAreAllGenerators($generators);
        $this->size = count($generators);
    }

    public function __invoke()
    {
        return array_map(
            function($generator) {
                return $generator();
            },
            $this->generators
        );
    }

    public function shrink($tuple)
    {
        if ($this->contains($tuple) && count($tuple) > 0) {
            $attemptsToShrink = 10;
            $numberOfElementsToShrink = rand(1, max(floor($this->size/2), 1));

            while ($numberOfElementsToShrink > 0 && $attemptsToShrink > 0) {
                $indexOfElementToShrink = rand(0, $this->size - 1);

                $shrinkedValue = $this->generators[$indexOfElementToShrink]
                    ->shrink($tuple[$indexOfElementToShrink]);

                if ($shrinkedValue === $tuple[$indexOfElementToShrink]) {
                    $attemptsToShrink--;
                    continue;
                }
                $numberOfElementsToShrink--;
                $tuple[$indexOfElementToShrink] = $shrinkedValue;
            }
        }
        return $tuple;
    }

    public function contains($tuple)
    {
        if (count($tuple) !== $this->size) {
            return false;
        }
        for ($i = 0; $i < $this->size; $i++) {
            if (!$this->generators[$i]->contains($tuple[$i])) {
                return false;
            }
        }
        return true;
    }
}
