<?php
namespace Eris\Generator;

class ElementsGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
        $this->rand = 'rand';
    }

    public function testGeneratesOnlyArgumentsInsideTheGivenArray()
    {
        $array = [1, 4, 5, 9];
        $generator = ElementsGenerator::fromArray($array);
        $generated = $generator($this->size, $this->rand);
        for ($i = 0; $i < 1000; $i++) {
            $this->assertContains(
                $generated->unbox(),
                $array
            );
        }
    }

    public function testASingleValueCannotShrinkGivenThereIsNoExplicitRelationshipBetweenTheValuesInTheDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 2, false]);
        $singleValue = GeneratedValueSingle::fromJustValue(2, 'elements');
        $this->assertEquals($singleValue, $generator->shrink($singleValue));
    }
}
