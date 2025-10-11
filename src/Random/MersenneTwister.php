<?php
namespace Eris\Random;

use RuntimeException;

class MersenneTwister implements Source
{
    private $index;
    private array $mt = [];
    private int $w = 32;
    private int $n = 624;
    private int $m = 397;
    private int $f = 1812433253;
    private int $wMask = 0xffffffff;
    // corresponds to $r = 31
    private int $lowerMask = 0x7fffffff;
    private int $upperMask = 0x80000000;
    // recurrence matrix
    private int $a = 0x9908b0df;
    // tempering
    private int $u = 11;
    private int $d = 0xffffffff;
    private int $s = 7;
    private int $b = 0x9d2c5680;
    private int $t = 15;
    private int $c = 0xefc60000;
    private int $l = 18;

    public function __construct()
    {
        if (defined('HHVM_VERSION')) {
            throw new RuntimeException("Pure PHP random implemnentation segfaults HHVM, so it's not available for this platform");
        }
    }
    
    public function seed($seed): static
    {
        $this->index = $this->n;
        $this->mt[0] = $seed & $this->wMask;
        for ($i = 1; $i <= $this->n - 1; $i++) {
            $this->mt[$i] = ($this->f * (
                $this->mt[$i - 1] ^ (($this->mt[$i - 1] >> ($this->w - 2)) & 0b11)
            ) + $i) & $this->wMask;
            assert($this->mt[$i] <= $this->wMask);
        }
        assert(count($this->mt) === 624);
        return $this;
    }

    public function extractNumber(): int
    {
        assert($this->index <= $this->n);
        if ($this->index >= $this->n) {
            $this->twist();
        }
        $y = $this->mt[$this->index];
        $y ^= ($y >> $this->u) & $this->d;
        assert($y <= 0xffffffff);
        $y ^= ($y << $this->s) & $this->b;
        assert($y <= 0xffffffff);
        $y ^= ($y << $this->t) & $this->c;
        assert($y <= 0xffffffff);
        $y ^= $y >> $this->l;
        assert($y <= 0xffffffff);
        $this->index += 1;
        return $y & $this->wMask;
    }

    public function max(): int
    {
        return 0xffffffff;
    }

    private function twist(): void
    {
        for ($i = 0; $i <= $this->n - 1; $i++) {
            $x = ($this->mt[$i] & $this->upperMask)
               + (($this->mt[($i+1) % $this->n]) & $this->lowerMask);
            assert($x <= 0xffffffff);
            $xA = $x >> 1;
            assert($xA <= 0x7fffffff);
            if ($x % 2 !== 0) {
                $xA ^= $this->a;
            }
            $this->mt[$i] = $this->mt[($i + $this->m) % $this->n] ^ $xA;
        }
        $this->index = 0;
    }
}
