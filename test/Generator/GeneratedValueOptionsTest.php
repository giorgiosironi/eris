<?php
namespace Eris\Generator;

// TODO: complete *unit* test coverage
class GeneratedValueOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testCartesianProductWithOtherValues()
    {
        $former = new GeneratedValueOptions([
            GeneratedValue::fromJustValue('a'),
            GeneratedValue::fromJustValue('b'),
        ]);
        $latter = new GeneratedValueOptions([
            GeneratedValue::fromJustValue('1'),
            GeneratedValue::fromJustValue('2'),
            GeneratedValue::fromJustValue('3'),
        ]);
        $product = $former->cartesianProduct($latter, function ($first, $second) { return $first . $second; });
        $this->assertEquals(6, count($product));
        foreach ($product as $value) {
            $this->assertRegexp('/^[ab][123]$/', $value->unbox());
        }
    }
}
