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
            'number' => $this->genInt(),
        ])
            ->__invoke(function($number) {
                $this->assertEquals(
                    $number,
                    my_sum($number, 0)
                );
            });
    }
}
