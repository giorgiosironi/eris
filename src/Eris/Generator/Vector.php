<?php
namespace Eris\Generator;
use Eris\Generator;

function vector($size, Generator $elementsGenerator)
{
    return new Vector($size, $elementsGenerator);
}

class Vector implements Generator
{
    private $size;
    private $elementsGenerator;

    public function __construct($size, Generator $elementsGenerator)
    {
        $this->size = $size;
        $this->elementsGenerator = $elementsGenerator;
    }

    public function __invoke()
    {
        $generator = $this->elementsGenerator;
        $sampleVector = [];
        for ($i = 0; $i < $this->size; $i++) {
            $sampleVector[] = $generator();
        }

        return $sampleVector;
    }

    public function shrink($vector)
    {
        $elementsToShrink = rand(1, (int)($this->size/2));
        $attempts = 10;

        while ($elementsToShrink > 0 && $attempts > 0) {
            $elementToShrink = rand(0, $this->size - 1);

            $shrinkedValue = $this->elementsGenerator->shrink($vector[$elementToShrink]);

            if ($shrinkedValue === $vector[$elementToShrink]) {
                $attempts--;
                continue;
            }
            $elementsToShrink--;
            $vector[$elementToShrink] = $shrinkedValue;
        }

        return $vector;
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
}
