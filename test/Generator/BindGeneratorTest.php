<?php
namespace Eris\Generator;

class BindGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
        $this->rand = 'rand';
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new BindGenerator(
            // TODO: order of parameters should be consistent with map, or not?
            ConstantGenerator::box(4),
            function ($n) {
                return new ChooseGenerator($n, $n+10);
            }
        );
        $this->assertInternalType(
            'integer',
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOuterGenerator()
    {
        $generator = new BindGenerator(
            new ChooseGenerator(0, 5),
            function ($n) {
                return new ChooseGenerator($n, $n + 10);
            }
        );
        $value = $generator->__invoke($this->size, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $this->assertInternalType(
                'integer',
                $value->unbox()
            );
            $value = $generator->shrink($value);
        }
        $this->assertLessThanOrEqual(5, $value->unbox());
    }

    public function testAssociativeProperty()
    {
        $firstGenerator = new BindGenerator(
            new BindGenerator(
                new ChooseGenerator(0, 5),
                function ($n) {
                    return new ChooseGenerator($n * 10, $n * 10 + 1);
                }
            ),
            function ($m) {
                return new VectorGenerator($m, new IntegerGenerator());
            }
        );
        $secondGenerator = new BindGenerator(
            new ChooseGenerator(0, 5),
            function ($n) {
                return new BindGenerator(
                    new ChooseGenerator($n * 10, $n * 10 + 1),
                    function ($m) {
                        return new VectorGenerator($m, new IntegerGenerator());
                    }
                );
            }
        );
        for ($i = 0; $i < 100; $i++) {
            $this->assertIsAnArrayOfX0OrX1Elements($firstGenerator->__invoke($this->size, $this->rand)->unbox());
            $this->assertIsAnArrayOfX0OrX1Elements($secondGenerator->__invoke($this->size, $this->rand)->unbox());
        }
    }

    private function assertIsAnArrayOfX0OrX1Elements(array $value)
    {
        $this->assertTrue(
            in_array(
                count($value) % 10,
                [0, 1]
            ),
            "The array has " . count($value) . " elements"
        );
    }
}
