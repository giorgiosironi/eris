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

    public function repeat($times)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->collected[] = $this->generator->__invoke(self::DEFAULT_SIZE)->unbox();
        }
        return $this;
    }

    public function shrink($nextValue = null)
    {
        if ($nextValue === null) {
            $nextValue = $this->generator->__invoke(self::DEFAULT_SIZE);
        }
        $this->collected[] = $nextValue->unbox();
        while ($value = $this->generator->shrink($nextValue)) {
            if ($value->unbox() === $nextValue->unbox()) {
                break;
            }
            $this->collected[] = $value->unbox();
            $nextValue = $value;
        }
        return $this;
    }

    public function collected()
    {
        return $this->collected;
    }
}
