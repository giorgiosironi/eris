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
            $this->collected[] = $this->generator->__invoke();
        }        
        return $this;
    }

    public function shrink($nextValue = null)
    {
        if ($nextValue === null) {
            $nextValue = $this->generator->__invoke();
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
