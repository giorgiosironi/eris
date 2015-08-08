<?php
namespace Eris;

class Sample
{
    const DEFAULT_SIZE = 10;

    private $generator;
    private $collected = [];

    public static function of($generator)
    {
        return new self($generator);
    }

    private function __construct($generator)
    {
        $this->generator = $generator;
    }

    public function numberOfSamples($nOfSamples)
    {
        for ($i = 0; $i < $nOfSamples; $i++) {
            $this->collected[] = $this->generator->__invoke(self::DEFAULT_SIZE);
        }
        return $this;
    }

    public function shrink($nextValue = null)
    {
        if ($nextValue === null) {
            $nextValue = $this->generator->__invoke(self::DEFAULT_SIZE);
        }
        $this->collected[] = $nextValue;
        while ($value = $this->generator->shrink($nextValue)) {
            if ($value === $nextValue) {
                break;
            }
            $this->collected[] = $value;
            $nextValue = $value;
        }
        return $this;
    }

    public function collected()
    {
        return $this->collected;
    }
}
