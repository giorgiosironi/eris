<?php
namespace Eris\Generator;

class FrequencyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }

    public function testEqualProbability()
    {
        $generator = new FrequencyGenerator([
            [1, 42],
            [1, 21],
        ]);

        $countOf = $this->distribute($generator);

        $this->assertTrue(
            abs($countOf[42] - $countOf[21]) < 100,
            'Generators have the same frequency but one is chosen more often than the other'
        );
    }

    public function testMoreFrequentGeneratorIsChosenMoreOften()
    {
        $generator = new FrequencyGenerator([
            [10, 42],
            [1, 21],
        ]);

        $countOf = $this->distribute($generator);
        $this->assertTrue(
            $countOf[42] > $countOf[21],
            '21 got chosen more often then 42 even if it has a much lower frequency'
        );
    }

    public function testZeroFrequencyMeansItWillNotBeChosen()
    {
        $generator = new FrequencyGenerator([
            [0, 42],
            [1, 21],
        ]);

        $countOf = $this->distribute($generator);
        $this->assertArrayNotHasKey(42, $countOf);
        $this->assertEquals(1000, $countOf[21]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        new FrequencyGenerator([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFrequenciesMustBeNaturals()
    {
        new FrequencyGenerator([
            [10, 42],
            [false, 21],
        ]);
    }

    public function testShrinkDisjointDomains()
    {
        $generator = new FrequencyGenerator([
            [10, 42],
            [1, 21],
        ]);


        $valueGeneratedFromFirstGenerator = GeneratedValue::fromValueAndInput(
            42,
            GeneratedValue::fromJustValue(42, 'constant'),
            'frequency'
        )->annotate('original_generator', 0);
        $this->assertEquals(
            $valueGeneratedFromFirstGenerator,
            $generator->shrink($valueGeneratedFromFirstGenerator)
        );

        $valueGeneratedFromSecondGenerator = GeneratedValue::fromValueAndInput(
            21,
            GeneratedValue::fromJustValue(21, 'constant'),
            'frequency'
        )->annotate('original_generator', 1);
        $this->assertEquals(
            $valueGeneratedFromSecondGenerator,
            $generator->shrink($valueGeneratedFromSecondGenerator)
        );
    }

    public function testShrinkIntersectingDomainsOnlyShrinkInTheDomainThatOriginallyProducedTheValue()
    {
        $generator = new FrequencyGenerator([
            [5, new ChooseGenerator(1, 100)],
            [3, new ChooseGenerator(10, 100)],
        ]);

        $valueGeneratedFromFirstGenerator = GeneratedValue::fromValueAndInput(
            42,
            GeneratedValue::fromJustValue(42, 'constant'),
            'frequency'
        )->annotate('original_generator', 0);
        for ($i = 0; $i < 100; $i++) {
            $valueGeneratedFromFirstGenerator = $generator->shrink($valueGeneratedFromFirstGenerator);
        }

        $this->assertEquals(1, $valueGeneratedFromFirstGenerator->unbox());
    }

    private function distribute($generator)
    {
        $countOf = [];
        for ($i = 0; $i < 1000; $i++) {
            $value = $generator($this->size)->unbox();
            if (array_key_exists($value, $countOf)) {
                $countOf[$value] += 1;
            } else {
                $countOf[$value] = 1;
            }
        }
        return $countOf;
    }

}
