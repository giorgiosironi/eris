<?php
namespace Eris;

use Eris\Generator\GeneratedValueOptions;

class Sample
{
    const DEFAULT_SIZE = 10;

    private $generator;
    private $rand;
    private $size;
    private $collected = [];

    public static function of($generator, $rand, $size = null)
    {
        return new self($generator, $rand, $size);
    }

    private function __construct($generator, $rand, $size = null)
    {
        $this->size = isset($size) ? (int) $size : self::DEFAULT_SIZE;
        $this->generator = $generator;
        $this->rand = $rand;
    }

    public function repeat($times)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->collected[] = $this->generator->__invoke($this->size, $this->rand)->unbox();
        }
        return $this;
    }

    public function shrink($nextValue = null)
    {
        if ($nextValue === null) {
            $nextValue = $this->generator->__invoke($this->size, $this->rand);
        }
        $this->collected[] = $nextValue->unbox();
        while ($value = $this->generator->shrink($nextValue)) {
            if ($value->unbox() === $nextValue->unbox()) {
                break;
            }
            $this->collected[] = $value->unbox();
            $nextValue = GeneratedValueOptions::mostPessimisticChoice($value);
        }
        return $this;
    }

    public function collected()
    {
        return $this->collected;
    }
}
