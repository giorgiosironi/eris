<?php
namespace Eris\Generator;
use Eris\Generator;

class Vector implements Generator
{
    private $size;
    private $vector;
    private $lastGenerated;

    public function __construct($size = 1, Generator $elementsGenerator)
    {
        $this->size = $size;
        $this->vector = $this->initialize(
            $size,
            $elementsGenerator
        );
    }

    public function __invoke()
    {
        $this->lastGenerated = $this->generateElements();

        return $this->lastGenerated;
    }

    public function shrink()
    {
        $elementsToShrink = rand(1, (int)($this->size/2));

        for ($i = 0; $i < $elementsToShrink; $i++) {
            $elementToShrink = rand(0, $this->size - 1);

            $shrinkedElement = $this->vector[$elementToShrink]->shrink();
            $this->lastGenerated[$elementToShrink] = $shrinkedElement;
        }

        return $this->lastGenerated;
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

    private function generateElements()
    {
        $sampleVector = [];
        foreach ($this->vector as $elementGenerator) {
            $sampleVector[] = $elementGenerator();
        }

        return $sampleVector;
    }
}
