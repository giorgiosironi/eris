<?php
namespace Eris;

class Sample
{
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

    public function withSize($size)
    {
        for ($i = 0; $i < $size; $i++) {
            $this->collected[] = $this->generator->__invoke($size);
        }
        return $this;
    }

    public function shrink($nextValue = null)
    {
        if ($nextValue === null) {
            // TODO: size = 10 is good?
            $nextValue = $this->generator->__invoke(10);
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
