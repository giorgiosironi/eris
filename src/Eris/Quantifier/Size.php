<?php
namespace Eris\Quantifier;

class Size
{
    private $list;
    
    public static function withTriangleGrowth($maximum)
    {
        return self::generateList($maximum, __CLASS__ . '::triangleNumber');
    }

    public static function withLinearGrowth($maximum)
    {
        return self::generateList($maximum, __CLASS__ . '::linearGrowth');
    }

    private static function generateList($maximum, callable $growth)
    {
        $sizes = [];
        for ($x = 0; $x <= $maximum; $x++) {
            $candidateSize = call_user_func($growth, $x);
            if ($candidateSize <= $maximum) {
                $sizes[] = $candidateSize;
            } else {
                break;
            }
        }
        return new self($sizes);
    }

    private static function linearGrowth($n)
    {
        return $n;
    }

    /**
     * Growth which approximates (n^2)/2.
     * Returns the number of dots needed to compose a
     * triangle with n dots on a side.
     *
     * E.G.: when n=3 the function evaluates to 6
     *   .
     *  . .
     * . . .
     */
    private static function triangleNumber($n)
    {
        if ($n === 0) {
            return 0;
        }
        return ($n * ($n + 1)) / 2;
    }
    
    private function __construct(array $list)
    {
        $this->list = $list;
    }

    public function at($position)
    {
        $index = $position % count($this->list);
        return $this->list[$index];   
    }

}
