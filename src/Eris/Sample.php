<?php
namespace Eris;

class Sample
{
    const DEAFULT_SIZE_FOR_SHRINK = 10;

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
            $nextValue = $this->generator->__invoke(self::DEAFULT_SIZE_FOR_SHRINK);
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
