<?php
namespace Eris\Random;

use RuntimeException;

class MersenneTwister implements Source
{
    private $seed;
    private $index;
    private $mt = [];
    private $w = 32;
    private $n = 624;
    private $m = 397;
    private $f = 1812433253;
    private $wMask = 0xffffffff;
    // corresponds to $r = 31
    private $lowerMask = 0x7fffffff;
    private $upperMask = 0x80000000;
    // recurrence matrix
    private $a = 0x9908b0df;
    // tempering
    private $u = 11;
    private $d = 0xffffffff;
    private $s = 7;
    private $b = 0x9d2c5680;
    private $t = 15;
    private $c = 0xefc60000;
    private $l = 18;

    public function __construct()
    {
        if (defined('HHVM_VERSION')) {
            throw new RuntimeException("Pure PHP random implemnentation segfaults HHVM, so it's not available for this platform");
        }
    }
    
    public function seed($seed)
    {
        $this->seed = $seed;
        $this->index = $this->n;
        $this->mt[0] = $seed & $this->wMask;
        for ($i = 1; $i <= $this->n - 1; $i++) {
            $this->mt[$i] = ($this->f * (
                $this->mt[$i - 1] ^ (($this->mt[$i - 1] >> ($this->w - 2)) & 0b11)
            ) + $i) & $this->wMask;
            assert('$this->mt[$i] <= $this->wMask');
        }
        assert('count($this->mt) === 624');
        return $this;
    }

    public function extractNumber()
    {
        assert('$this->index <= $this->n');
        if ($this->index >= $this->n) {
            $this->twist();
        }
        $y = $this->mt[$this->index];
        $y = $y ^ (($y >> $this->u) & $this->d);
        assert('$y <= 0xffffffff');
        $y = $y ^ (($y << $this->s) & $this->b);
        assert('$y <= 0xffffffff');
        $y = $y ^ (($y << $this->t) & $this->c);
        assert('$y <= 0xffffffff');
        $y = $y ^ ($y >> $this->l);
        assert('$y <= 0xffffffff');
        $this->index = $this->index + 1;
        return $y & $this->wMask;
    }

    public function max()
    {
        return 0xffffffff;
    }

    private function twist()
    {
        for ($i = 0; $i <= $this->n - 1; $i++) {
            $x = ($this->mt[$i] & $this->upperMask)
               + (($this->mt[($i+1) % $this->n]) & $this->lowerMask);
            assert('$x <= 0xffffffff');
            $xA = $x >> 1;
            assert('$xA <= 0x7fffffff');
            if (($x % 2) != 0) {
                $xA = $xA ^ $this->a;
            }
            $this->mt[$i] = $this->mt[($i + $this->m) % $this->n] ^ $xA;
        }
        $this->index = 0;
    }
}
