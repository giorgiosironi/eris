<?php
namespace Eris;

use Eris\Generator\GeneratedValue;
use Eris\Generator\GeneratedValueOptions;

/**
 * @template T
 */
class Sample
{
    const DEFAULT_SIZE = 10;

    private $generator;
    private $rand;
    private $size;
    /**
     * @var list<T>
     */
    private $collected = [];

    /**
     * @template U
     * @param Generator<U> $generator
     * @param Random\RandomRange $rand
     * @param int|null $size
     * @return self<U>
     */
    public static function of($generator, $rand, $size = null)
    {
        return new self($generator, $rand, $size);
    }

    /**
     * @param Generator<T> $generator
     * @param Random\RandomRange $rand
     * @param int|null $size
     */
    private function __construct($generator, $rand, $size = null)
    {
        $this->size = isset($size) ? (int) $size : self::DEFAULT_SIZE;
        $this->generator = $generator;
        $this->rand = $rand;
    }

    /**
     * @param int $times
     * @return $this
     */
    public function repeat($times)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->collected[] = $this->generator->__invoke($this->size, $this->rand)->unbox();
        }
        return $this;
    }

    /**
     * @param GeneratedValue<T>|null $nextValue
     * @return $this
     */
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

    /**
     * @return list<T>
     */
    public function collected()
    {
        return $this->collected;
    }
}
