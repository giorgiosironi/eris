<?php
namespace Eris\Generator;

// TODO: complete *unit* test coverage
class GeneratedValueOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testMapsOverAllTheOptions()
    {
        $value = GeneratedValue::fromJustValue(42);
        $options = new GeneratedValueOptions([$value]);
        $double = function($n) { return $n * 2; };
        $this->assertEquals(
            new GeneratedValueOptions([$value->map($double, 'doubler')]),
            $options->map($double, 'doubler')
        );
    }

    public function testAddingAndRemoving()
    {
        $someOptions = new GeneratedValueOptions([
            GeneratedValue::fromJustValue(42),
            GeneratedValue::fromJustValue(43),
            GeneratedValue::fromJustValue(44),
        ]);
        $this->assertEquals(
            new GeneratedValueOptions([
                GeneratedValue::fromJustValue(44),
                GeneratedValue::fromJustValue(45),
                GeneratedValue::fromJustValue(46),
            ]),
            $someOptions
                ->add(GeneratedValue::fromJustValue(45))
                ->remove(GeneratedValue::fromJustValue(42))
                ->add(GeneratedValue::fromJustValue(46))
                ->remove(GeneratedValue::fromJustValue(43))
        );
    }

    public function testCount()
    {
        $this->assertEquals(
            3,
            count(new GeneratedValueOptions([
                GeneratedValue::fromJustValue(44),
                GeneratedValue::fromJustValue(45),
                GeneratedValue::fromJustValue(46),
            ]))
        );
    }
    
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
