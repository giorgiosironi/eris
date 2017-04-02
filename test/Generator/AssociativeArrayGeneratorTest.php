<?php
namespace Eris\Generator;

class AssociativeArrayGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->letterGenerator = ElementsGenerator::fromArray(['A', 'B', 'C']);
        $this->cipherGenerator = ElementsGenerator::fromArray([0, 1, 2]);
        $this->smallIntegerGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
        $this->rand = 'rand';
    }

    public function testConstructWithAnAssociativeArrayOfGenerators()
    {
        $generator = new AssociativeArrayGenerator([
            'letter' => $this->letterGenerator,
            'cipher' => $this->cipherGenerator,
        ]);

        $generated = $generator($this->size, $this->rand);

        $array = $generated->unbox();
        $this->assertEquals(2, count($array));
        $letter = $array['letter'];
        $this->assertInternalType('string', $letter);
        $this->assertEquals(1, strlen($letter));
        $cipher = $array['cipher'];
        $this->assertInternalType('integer', $cipher);
        $this->assertGreaterThanOrEqual(0, $cipher);
        $this->assertLessThanOrEqual(9, $cipher);
        $this->assertSame(2, count($generated->unbox()));
    }

    public function testShrinksTheGeneratorsButKeepsAllTheKeysPresent()
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
            $this->assertEquals(2, count($array));
            $this->assertEquals(
                ['former', 'latter'],
                array_keys($array)
            );
            $this->assertInternalType('integer', $array['former']);
            $this->assertInternalType('integer', $array['latter']);
        }
    }
}
