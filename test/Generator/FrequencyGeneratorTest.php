<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class FrequencyGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testEqualProbability(): void
    {
        $generator = new FrequencyGenerator([
            [1, 42],
            [1, 21],
        ]);

        $countOf = $this->distribute($generator);

        $this->assertTrue(
            abs($countOf[42] - $countOf[21]) < 100,
            'Generators have the same frequency but one is chosen more often than the other: ' . var_export($countOf, true)
        );
    }

    public function testMoreFrequentGeneratorIsChosenMoreOften(): void
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

    public function testZeroFrequencyMeansItWillNotBeChosen(): void
    {
        $generator = new FrequencyGenerator([
            [0, 42],
            [1, 21],
        ]);

        $countOf = $this->distribute($generator);
        $this->assertArrayNotHasKey(42, $countOf);
        $this->assertEquals(1000, $countOf[21]);
    }

    public function testConstructWithNoArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FrequencyGenerator([]);
    }

    public function testFrequenciesMustBeNaturals(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FrequencyGenerator([
            [10, 42],
            [false, 21],
        ]);
    }

    public function testShrinking(): void
    {
        $generator = new FrequencyGenerator([
            [10, 42],
            [1, 21],
        ]);

        for ($i = 0; $i < $this->size; $i++) {
            $element = $generator($this->size, $this->rand);
            $shrinked = $generator->shrink($element);
            $this->assertThat(
                $shrinked->unbox(),
                $this->logicalOr(
                    $this->equalTo(42),
                    $this->equalTo(21)
                )
            );
        }
    }

    public function testShrinkIntersectingDomainsOnlyShrinkInTheDomainThatOriginallyProducedTheValue(): void
    {
        $generator = new FrequencyGenerator([
            [5, new ChooseGenerator(1, 100)],
            [3, new ChooseGenerator(10, 100)],
        ]);

        $shrinkedTable = [];
        for ($i = 0; $i < 100; $i++) {
            $element = $generator($this->size, $this->rand);
            for ($j = 0; $j < 100; $j++) {
                $element = $generator->shrink($element);
            }
            $shrinkedTable[$element->unbox()] = true;
        }
        $this->assertEquals([1 => true, 10 => true], $shrinkedTable);
    }

    private function distribute($generator): array
    {
        $countOf = [];
        for ($i = 0; $i < 1000; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            if (array_key_exists($value, $countOf)) {
                $countOf[$value] += 1;
            } else {
                $countOf[$value] = 1;
            }
        }
        return $countOf;
    }
}
