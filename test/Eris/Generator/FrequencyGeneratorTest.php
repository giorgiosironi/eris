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

        $countOf = [42 => 0, 21 => 0];
        for ($i = 0; $i < 1000; $i++) {
            $countOf[$generator($this->size)] += 1;
        }

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

        $countOf = [42 => 0, 21 => 0];
        for ($i = 0; $i < 1000; $i++) {
            $countOf[$generator($this->size)] += 1;
        }
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

        $countOf = [42 => 0, 21 => 0];
        for ($i = 0; $i < 1000; $i++) {
            $countOf[$generator($this->size)] += 1;
        }
        $this->assertEquals(0, $countOf[42]);
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

        $this->assertEquals(42, $generator->shrink(42));
        $this->assertEquals(21, $generator->shrink(21));
    }

    public function testShrinkIntersectingDomains()
    {
        $generator = new FrequencyGenerator([
            [10, new ChooseGenerator(1, 100)],
            [1, new ChooseGenerator(10, 100)],
        ]);

        $element = 42;
        for ($i = 0; $i < 100; $i++) {
            $element = $generator->shrink($element);
        }

        $this->assertEquals(1, $element);
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkSomethingThatIsNotInDomain()
    {
        $generator = new FrequencyGenerator([
            [10, 42],
            [1, 21],
        ]);

        $generator->shrink('something');
    }
}
