<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use PHPUnit\Framework\TestCase;

class AssociativeArrayGeneratorTest extends TestCase
{
    /**
     * @var ElementsGenerator
     */
    private $letterGenerator;
    /**
     * @var ElementsGenerator
     */
    private $cipherGenerator;
    /**
     * @var ChooseGenerator
     */
    private $smallIntegerGenerator;
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
        $this->letterGenerator = ElementsGenerator::fromArray(['A', 'B', 'C']);
        $this->cipherGenerator = ElementsGenerator::fromArray([0, 1, 2]);
        $this->smallIntegerGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testConstructWithAnAssociativeArrayOfGenerators(): void
    {
        $generator = new AssociativeArrayGenerator([
            'letter' => $this->letterGenerator,
            'cipher' => $this->cipherGenerator,
        ]);

        $generated = $generator($this->size, $this->rand);

        $array = $generated->unbox();
        $this->assertCount(2, $array);
        $letter = $array['letter'];
        \Eris\PHPUnitDeprecationHelper::assertIsString($letter);
        $this->assertEquals(1, strlen($letter));
        $cipher = $array['cipher'];
        \Eris\PHPUnitDeprecationHelper::assertIsInt($cipher);
        $this->assertGreaterThanOrEqual(0, $cipher);
        $this->assertLessThanOrEqual(9, $cipher);
        $this->assertCount(2, $generated->unbox());
    }

    public function testShrinksTheGeneratorsButKeepsAllTheKeysPresent(): void
    {
        $generator = new AssociativeArrayGenerator([
            'former' => $this->smallIntegerGenerator,
            'latter' => $this->smallIntegerGenerator,
        ]);

        $value = $generator($this->size, $this->rand);

        for ($i = 0; $i < 100; $i++) {
            $value = GeneratedValueOptions::mostPessimisticChoice($value);
            $value = $generator->shrink($value);
            $array = $value->unbox();
            $this->assertCount(2, $array);
            $this->assertEquals(
                ['former', 'latter'],
                array_keys($array)
            );
            \Eris\PHPUnitDeprecationHelper::assertIsInt($array['former']);
            \Eris\PHPUnitDeprecationHelper::assertIsInt($array['latter']);
        }
    }
}
