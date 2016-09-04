<?php
namespace Eris\Generator;

// TODO: complete *unit* test coverage
class GeneratedValueOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testMapsOverAllTheOptions()
    {
        $value = GeneratedValueSingle::fromJustValue(42);
        $options = new GeneratedValueOptions([$value]);
        $double = function ($n) {
            return $n * 2;
        };
        $this->assertEquals(
            new GeneratedValueOptions([$value->map($double, 'doubler')]),
            $options->map($double, 'doubler')
        );
    }

    public function testAddingAndRemoving()
    {
        $someOptions = new GeneratedValueOptions([
            GeneratedValueSingle::fromJustValue(42),
            GeneratedValueSingle::fromJustValue(43),
            GeneratedValueSingle::fromJustValue(44),
        ]);
        $this->assertEquals(
            new GeneratedValueOptions([
                GeneratedValueSingle::fromJustValue(44),
                GeneratedValueSingle::fromJustValue(45),
                GeneratedValueSingle::fromJustValue(46),
            ]),
            $someOptions
                ->add(GeneratedValueSingle::fromJustValue(45))
                ->remove(GeneratedValueSingle::fromJustValue(42))
                ->add(GeneratedValueSingle::fromJustValue(46))
                ->remove(GeneratedValueSingle::fromJustValue(43))
        );
    }

    public function testCount()
    {
        $this->assertEquals(
            3,
            count(new GeneratedValueOptions([
                GeneratedValueSingle::fromJustValue(44),
                GeneratedValueSingle::fromJustValue(45),
                GeneratedValueSingle::fromJustValue(46),
            ]))
        );
    }
    
    public function testCartesianProductWithOtherValues()
    {
        $former = new GeneratedValueOptions([
            GeneratedValueSingle::fromJustValue('a'),
            GeneratedValueSingle::fromJustValue('b'),
        ]);
        $latter = new GeneratedValueOptions([
            GeneratedValueSingle::fromJustValue('1'),
            GeneratedValueSingle::fromJustValue('2'),
            GeneratedValueSingle::fromJustValue('3'),
        ]);
        $product = $former->cartesianProduct($latter, function ($first, $second) {
            return $first . $second;
        });
        $this->assertEquals(6, count($product));
        foreach ($product as $value) {
            $this->assertRegexp('/^[ab][123]$/', $value->unbox());
        }
    }
}
