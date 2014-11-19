<?php
namespace Eris\Generator;
use Eris\Generator;

class Vector implements Generator
{
    private $size;
    private $generators;
    private $elementsGenerator;
    private $lastGeneratedVector;

    public function __construct($size = 1, Generator $elementsGenerator)
    {
        $this->size = $size;
        $this->generators = $this->initialize(
            $size,
            $elementsGenerator
        );
        $this->elementsGenerator = $elementsGenerator;
    }

    public function __invoke()
    {
        $this->lastGeneratedVector = $this->pick();

        return $this->lastGeneratedVector;
    }

    public function shrink()
    {
        $elementsToShrink = rand(1, (int)($this->size/2));
        $attempts = 10;

        while ($elementsToShrink > 0 && $attempts > 0) {
            $elementToShrink = rand(0, $this->size - 1);

            $shrinkedValue = $this->generators[$elementToShrink]->shrink();

            if ($shrinkedValue === $this->lastGeneratedVector[$elementToShrink]) {
                $attempts--;
                continue;
            }
            $elementsToShrink--;
            $this->lastGeneratedVector[$elementToShrink] = $shrinkedValue;
        }

        return $this->lastGeneratedVector;
    }

    public function contains($generatedVector)
    {
        return (count($generatedVector) === $this->size)
            && $this->containsElement($generatedVector);
    }

    private function containsElement($generatedVector)
    {
        foreach ($generatedVector as $elementInVector) {
            if (!$this->elementsGenerator->contains($elementInVector)) {
                return false;
            }
        }

        return true;
    }

    private function initialize($size, Generator $generator)
    {
        $vector = [];
        for ($i = 0; $i < $size; $i++) {
            $elementGenerator = clone $generator;
            $vector[] = $elementGenerator;
        }

        return $vector;
    }

    private function pick()
    {
        $sampleVector = [];
        foreach ($this->generators as $elementGenerator) {
            $sampleVector[] = $elementGenerator();
        }

        return $sampleVector;
    }
}
