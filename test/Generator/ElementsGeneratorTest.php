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

    public function testOnlyContainsTheElementsGeneratorOfTheGivenDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 2]);
        $this->assertFalse($generator->contains(GeneratedValueSingle::fromJustValue(1)));
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue('A')));
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue(2)));
        // disregarding types
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue('2')));
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 1]);
        $generator->shrink(GeneratedValueSingle::fromJustValue(2));
    }
}
