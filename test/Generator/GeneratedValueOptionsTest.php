<?php
namespace Eris\Generator;

// TODO: complete *unit* test coverage
class GeneratedValueOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testMapsOverAllTheOptions(): void
    {
        $value = GeneratedValueSingle::fromJustValue(42);
        $options = new GeneratedValueOptions([$value]);
        $double = (fn($n): int|float => $n * 2);
        $this->assertEquals(
            new GeneratedValueOptions([$value->map($double, 'doubler')]),
            $options->map($double, 'doubler')
        );
    }

    public function testAddingAndRemoving(): void
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

    public function testCount(): void
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
    
    public function testCartesianProductWithOtherValues(): void
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
        $product = $former->cartesianProduct($latter, fn($first, $second): string => $first . $second);
        $this->assertEquals(6, count($product));
        foreach ($product as $value) {
            self::assertMatchesRegularExpression('/^[ab][123]$/', $value->unbox());
        }
    }
}
