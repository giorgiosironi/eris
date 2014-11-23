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

    public function shrink()
    {
        $lastValues = $this->generator->__invoke();
        $this->collected[] = $lastValues;
        while ($values = $this->generator->shrink($lastValues)) {
            if ($values === $lastValues) {
                break;
            }
            $this->collected[] = $values;
            $lastValues = $values;
        }
        return $this;
    }
}
