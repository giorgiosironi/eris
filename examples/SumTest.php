<?php
function my_sum($first, $second)
{
    if ($first >= 42) {
        return $first + $second + 1;
    }
    return $first + $second;
}

class ListConcatenationTest extends BaseTestCase
{
    public function testRightIdentityElement()
    {
        $this->forAll([
            $this->genInt(),
        ])
            ->__invoke(function($number) {
                $this->assertEquals(
                    $number,
                    my_sum($number, 0),
                    "Summing $number to 0"
                );
            });
    }

    public function testLeftIdentityElement()
    {
        $this->forAll([
            $this->genInt(),
        ])
            ->__invoke(function($number) {
                $this->assertEquals(
                    $number,
                    my_sum(0, $number),
                    "Summing 0 to $number"
                );
            });
    }

    public function testEqualToReferencePhpImplementation()
    {
        $this->forAll([
            $this->genInt(),
            $this->genInt(),
        ])
            ->__invoke(function($first, $second) {
                $this->assertEquals(
                    $first + $second,
                    my_sum($first, $second),
                    "Summing $first and $second"
                );
            });
    }
}
