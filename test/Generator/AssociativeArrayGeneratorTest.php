<?php
namespace Eris\Generator;

class AssociativeArrayGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->letterGenerator = ElementsGenerator::fromArray(['A', 'B', 'C']);
        $this->cipherGenerator = ElementsGenerator::fromArray([0, 1, 2]);
        $this->smallIntegerGenerator = new ChooseGenerator(0, 100);
        $this->size = 10;
    }

    public function testConstructWithAnAssociativeArrayOfGenerators()
    {
        $generator = new AssociativeArrayGenerator([
            'letter' => $this->letterGenerator,
            'cipher' => $this->cipherGenerator,
        ]);

        $generated = $generator($this->size);

        $this->assertTrue($generator->contains($generated));
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

        $value = $generator($this->size);

        for ($i = 0; $i < 100; $i++) {
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
